<?php

namespace App\Services\Socket;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Services\BookingService;
use Laravel\Sanctum\PersonalAccessToken;
use OpenSwoole\WebSocket\Server;
use Illuminate\Support\Facades\Log;


class SocketServer
{
    protected Server $server;
    protected DriverPresenceStore $presenceStore; // @phpstan-ignore-line
    protected BookingService $bookingService; // @phpstan-ignore-line

    public array $connections = [];
    /** @var array<int, int> */
    public array $drivers = [];
    public array $users = [];
    Protected array $driverLocations = [];  // driver_id => lat/lng

    public function __construct()
    {
        $this->presenceStore = app(DriverPresenceStore::class);
        $this->bookingService = app(BookingService::class);
    }


    public function start(): void
    {
        $this->server = new Server('0.0.0.0', 9502);

        $this->server->on('Start', function () {
            echo "Socket Server Started\n";
        });

        $this->server->on('Open', function ($server, $request) {

            echo "Client Connected : {$request->fd}\n";

            $token = $request->get['token'] ?? null;

            if (!$token) {
                $server->push($request->fd, json_encode([
                    'type' => 'auth_error',
                    'message' => 'Token missing'
                ]));

                $server->disconnect($request->fd);
                return;
            }

            if (!$this->authenticate($request->fd, $token)) {

                $server->push($request->fd, json_encode([
                    'type' => 'auth_error',
                    'message' => 'Invalid token'
                ]));

                $server->disconnect($request->fd);
                return;
            }

            $server->push($request->fd, json_encode([
                'type' => 'connected',
                'message' => 'Authenticated'
            ]));
        });

        $this->server->on('Request', function ($request, $response) {
            $path = trim($request->server['request_uri'] ?? '/', '/');
            $payload = json_decode($request->rawContent() ?: '', true);

            if (! is_array($payload)) {
                $payload = $request->post ?? [];
            }

            logger()->info('Socket request received', [
                'path' => $path,
                'payload' => $payload,
            ]);

            $response->header('Content-Type', 'application/json');

            if (! is_array($payload)) {
                $response->status(400);
                $response->end(json_encode([
                    'type' => 'error',
                    'message' => 'Invalid request payload',
                ]));

                return;
            }

            if (in_array($path, ['send_booking', 'send-booking'], true)) {

                Log::info('Booking request received', [
                    'payload' => $payload,
                ]);

                $driverIds = $this->normalizeDriverIds(
                    $payload['driver_ids'] ?? ($payload['driver_id'] ?? [])
                );

                Log::info('Normalized driver IDs', [
                    'driver_ids' => $driverIds,
                ]);

                if (empty($driverIds)) {

                    Log::warning('No driver IDs provided');

                    $this->markBookingNoDriverAvailable($payload['booking']['id'] ?? null, $payload['booking']['user_id'] ?? null);

                    $response->status(422);
                    $response->end(json_encode([
                        'type' => 'error',
                        'message' => 'No driver ids were provided',
                    ]));

                    return;
                }

                if (
                    isset($payload['latitude'], $payload['longitude'])
                    && is_numeric($payload['latitude'])
                    && is_numeric($payload['longitude'])
                ) {

                    Log::info('Finding nearby drivers', [
                        'latitude' => $payload['latitude'],
                        'longitude' => $payload['longitude'],
                        'radius' => $payload['radius'] ?? 5,
                    ]);

                    $nearbyDriverIds = $this->presenceStore->findNearbyDriverIds(
                        (float) $payload['latitude'],
                        (float) $payload['longitude'],
                        (float) ($payload['radius'] ?? 5)
                    );

                    Log::info('Nearby drivers found', [
                        'nearby_driver_ids' => $nearbyDriverIds,
                    ]);

                    if (!empty($nearbyDriverIds)) {
                        $driverIds = array_values(array_intersect($driverIds, $nearbyDriverIds));

                        Log::info('Drivers after nearby filter', [
                            'driver_ids' => $driverIds,
                        ]);
                    }
                }

                if (empty($driverIds)) {

                    Log::warning('No matching online drivers after filtering');

                    $this->markBookingNoDriverAvailable($payload['booking']['id'] ?? null, $payload['booking']['user_id'] ?? null);

                    $response->status(404);
                    $response->end(json_encode([
                        'type' => 'error',
                        'message' => 'No online drivers matched the booking request',
                    ]));

                    return;
                }

                $bookingPayload = [
                    'type' => 'booking_request',
                    'booking' => $payload['booking'] ?? [],
                ];

                Log::info('Booking payload prepared', [
                    'booking_payload' => $bookingPayload,
                ]);

                $sent = 0;

                foreach ($driverIds as $driverId) {

                    Log::info("Processing driver {$driverId}");

                    $driverFd = $this->presenceStore->getDriverFd($driverId);

                    Log::info('Driver FD lookup', [
                        'driver_id' => $driverId,
                        'fd' => $driverFd,
                    ]);

                    if (!$driverFd) {

                        Log::warning("Driver {$driverId} has no FD");

                        continue;
                    }

                    if ($this->server->isEstablished($driverFd)) {

                        Log::info("Sending booking to driver {$driverId}", [
                            'fd' => $driverFd,
                        ]);

                        $this->server->push($driverFd, json_encode($bookingPayload));

                        Log::info("Booking sent successfully to driver {$driverId}");

                        $sent++;
                    } else {

                        Log::warning("Driver {$driverId} socket not established", [
                            'fd' => $driverFd,
                        ]);
                    }
                }

                Log::info('Booking dispatch completed', [
                    'sent' => $sent,
                    'driver_ids' => $driverIds,
                ]);

                if ($sent === 0) {

                    Log::warning('All matching drivers are offline');

                    $this->markBookingNoDriverAvailable($payload['booking']['id'] ?? null, $payload['booking']['user_id'] ?? null);

                    $response->status(404);
                    $response->end(json_encode([
                        'type' => 'error',
                        'message' => 'All matching drivers are offline',
                    ]));

                    return;
                }

                // Register a background timeout of 60 seconds (60000ms)
                if (isset($booking['id'])) {
                    $bookingId = (int) $booking['id'];
                    $userId = isset($booking['user_id']) ? (int) $booking['user_id'] : null;

                    \OpenSwoole\Timer::after(60000, function () use ($bookingId, $userId) {
                        try {
                            $dbBooking = \App\Models\Booking::find($bookingId);
                            if ($dbBooking && in_array($dbBooking->status, [Booking::STATUS_PENDING, Booking::STATUS_SEARCHING_DRIVER], true)) {
                                $dbBooking->update([
                                    'status' => Booking::STATUS_NO_DRIVER_AVAILABLE,
                                ]);

                                Log::info('Booking dispatch timed out, no driver accepted.', [
                                    'booking_id' => $bookingId,
                                    'booking_no' => $dbBooking->booking_no,
                                ]);

                                // Broadcast to the user over WebSocket
                                $payload = [
                                    'type' => 'booking_status',
                                    'booking' => (new BookingResource($dbBooking))->resolve(),
                                ];

                                if ($userId) {
                                    $userFd = $this->presenceStore->getUserFd($userId);
                                    if ($userFd && $this->server->isEstablished($userFd)) {
                                        $this->server->push($userFd, json_encode($payload));
                                        Log::info('Sent no_driver_available status update to user socket.', [
                                            'user_id' => $userId,
                                            'fd' => $userFd,
                                        ]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error('Error processing background dispatch timeout.', [
                                'booking_id' => $bookingId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    });
                }

                $response->end(json_encode([
                    'type' => 'success',
                    'message' => 'Booking sent',
                    'driver_ids' => $driverIds,
                    'sent' => $sent,
                ]));

                Log::info('Booking request finished successfully');

                return;
            }

            if ($path === 'broadcast-booking-update') {
                $booking = $payload['booking'] ?? [];
                $message = [
                    'type' => 'booking_status',
                    'booking' => $booking,
                ];

                $targets = [];

                if (isset($booking['driver_id'])) {
                    $driverFd = $this->presenceStore->getDriverFd((int) $booking['driver_id']);
                    if ($driverFd) {
                        $targets['driver:' . (int) $booking['driver_id']] = $driverFd;
                    }
                }

                if (isset($booking['user_id'])) {
                    $userFd = $this->presenceStore->getUserFd((int) $booking['user_id']);
                    if ($userFd) {
                        $targets['user:' . (int) $booking['user_id']] = $userFd;
                    }
                }

                foreach ($targets as $fd) {
                    if ($this->server->isEstablished($fd)) {
                        $this->server->push($fd, json_encode($message));
                    }
                }

                $response->end(json_encode([
                    'type' => 'success',
                    'message' => 'Booking update broadcast',
                    'targets' => count($targets),
                ]));

                return;
            }

            $response->status(404);
            $response->end(json_encode([
                'type' => 'error',
                'message' => 'Unknown route',
            ]));
        });

        $this->server->on('Message', function ($server, $frame) {

            echo "---------------------------------\n";
            echo "FD : {$frame->fd}\n";
            echo "Message : {$frame->data}\n";
            echo "---------------------------------\n";

            $payload = json_decode($frame->data, true);

            if (!is_array($payload)) {
                $server->push($frame->fd, json_encode([
                    'type' => 'error',
                    'message' => 'Invalid JSON',
                ]));
                return;
            }

            switch ($payload['type'] ?? '') {

                case 'driver_location':

                    if (!isset($this->connections[$frame->fd])) {
                        break;
                    }

                    $connection = $this->connections[$frame->fd];

                    if ($connection['type'] !== 'driver') {
                        break;
                    }

                    $driverId = $connection['id'];

                    $this->driverLocations[$driverId] = [
                        'latitude'  => (float) $payload['latitude'],
                        'longitude' => (float) $payload['longitude'],
                        'updated_at' => time(),
                    ];

                    $this->presenceStore->updateDriverLocation(
                        $driverId,
                        (float) $payload['latitude'],
                        (float) $payload['longitude']
                    );

                    // Persist to database vehicle_locations table
                    try {
                        $vehicle = \App\Models\Vehicle::where('driver_id', $driverId)->where('status', 'active')->first();
                        if ($vehicle) {
                            \App\Models\VehicleLocation::create([
                                'vehicle_id' => $vehicle->id,
                                'latitude' => (float) $payload['latitude'],
                                'longitude' => (float) $payload['longitude'],
                                'location_updated_at' => now(),
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to write vehicle location to DB', ['error' => $e->getMessage()]);
                    }

                    // Broadcast to active passenger
                    try {
                        $activeBooking = \App\Models\Booking::where('driver_id', $driverId)
                            ->whereIn('status', [Booking::STATUS_ACCEPTED, Booking::STATUS_STARTED])
                            ->first();

                        if ($activeBooking) {
                            $userId = $activeBooking->user_id;
                            $userFd = $this->presenceStore->getUserFd((int) $userId);
                            if ($userFd && $this->server->isEstablished($userFd)) {
                                $this->server->push($userFd, json_encode([
                                    'type' => 'driver_location_update',
                                    'booking_no' => $activeBooking->booking_no,
                                    'latitude' => (float) $payload['latitude'],
                                    'longitude' => (float) $payload['longitude'],
                                    'bearing' => (float) ($payload['bearing'] ?? 0),
                                ]));
                                Log::info('Broadcasted driver location to passenger.', [
                                    'driver_id' => $driverId,
                                    'user_id' => $userId,
                                    'fd' => $userFd,
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to broadcast driver location to passenger', ['error' => $e->getMessage()]);
                    }

                    $server->push($frame->fd, json_encode([
                        'type' => 'location_updated',
                    ]));

                    break;

                case 'accept_booking':

                    echo "Driver accepted booking\n";

                    break;

                case 'reject_booking':

                    echo "Driver rejected booking\n";

                    break;
                case 'send_booking':
                    $driverId = (int) ($payload['driver_id'] ?? 0);

                    if (!isset($this->drivers[$driverId])) {

                        $server->push($frame->fd, json_encode([
                            'type' => 'error',
                            'message' => 'Driver is offline',
                        ]));

                        break;
                    }

                    $driverFd = $this->drivers[$driverId];

                    $server->push($driverFd, json_encode([
                        'type' => 'booking_request',
                        'booking' => $payload['booking'],
                    ]));

                    $server->push($frame->fd, json_encode([
                        'type' => 'success',
                        'message' => 'Booking sent',
                    ]));

                    break;

                case 'ping':

                    $server->push($frame->fd, json_encode([
                        'type' => 'pong',
                    ]));

                    break;

                default:

                    $server->push($frame->fd, json_encode([
                        'type' => 'error',
                        'message' => 'Unknown event',
                    ]));
            }
        });

        $this->server->on('Close', function ($server, $fd) {

            if (! isset($this->connections[$fd])) {
                return;
            }

            $connection = $this->connections[$fd];

            if ($connection['type'] === 'driver') {

                unset($this->drivers[$connection['id']]);
                unset($this->driverLocations[$connection['id']]);
                $this->presenceStore->forgetDriver($connection['id']);

            } else {

                unset($this->users[$connection['id']]);
                $this->presenceStore->forgetUser($connection['id']);
            }

            unset($this->connections[$fd]);
            echo "Disconnected: {$fd}\n";
        });

        $this->server->start();
    }

    protected function authenticate(int $fd, string $token): bool
    {
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return false;
        }

        $user = $accessToken->tokenable;

        $this->connections[$fd] = [
            'fd' => $fd,
            'id' => $user->id,
            'type' => $user instanceof Driver ? 'driver' : 'user',
        ];

        $type = $user instanceof Driver ? 'driver' : 'user';

        if ($type === 'driver') {
            $this->drivers[$user->id] = $fd;
            $this->presenceStore->setDriverConnection($user->id, $fd);
        } else {
            $this->users[$user->id] = $fd;
            $this->presenceStore->setUserConnection($user->id, $fd);
        }

        if ($user instanceof User) {
            $activeBooking = $this->bookingService->userActiveBooking($user);

                if ($activeBooking) {
                    $activeBooking->load([
                        'category.pricing',
                        'user',
                        'locations',
                    'pickupLocation',
                    'dropLocation',
                    'usage',
                    'fare',
                    'driver',
                        'vehicle',
                        'user',
                    ]);

                    $bookingPayload = (new BookingResource($activeBooking))->resolve();

                    if ($activeBooking->status === Booking::STATUS_STARTED && ! empty($activeBooking->start_otp)) {
                        $bookingPayload['start_otp'] = $activeBooking->start_otp;
                    }

                    if ($this->server->isEstablished($fd)) {
                        $this->server->push($fd, json_encode([
                            'type' => 'booking_status',
                            'booking' => $bookingPayload,
                        ]));
                    }
                }
        }

        print_r($this->connections);
        print_r($this->users);
        print_r($this->drivers);
        echo "Authenticated {$this->connections[$fd]['type']} : {$user->id}\n";

        return true;
    }

    protected function markBookingNoDriverAvailable($bookingId, $userId = null): void
    {
        if (! $bookingId) {
            return;
        }

        $booking = Booking::query()->find((int) $bookingId);

        if (! $booking) {
            return;
        }

        if (in_array($booking->status, Booking::TERMINAL_STATUSES, true)) {
            return;
        }

        if (! in_array($booking->status, [
            Booking::STATUS_PENDING,
            Booking::STATUS_REQUESTED,
            Booking::STATUS_SEARCHING_DRIVER,
        ], true)) {
            return;
        }

        $booking->update([
            'status' => Booking::STATUS_NO_DRIVER_AVAILABLE,
        ]);

        $booking->loadMissing([
            'category.pricing',
            'user',
            'driver',
            'vehicle',
            'locations',
            'pickupLocation',
            'dropLocation',
            'usage',
            'fare',
        ]);

        $payload = [
            'type' => 'booking_status',
            'booking' => (new BookingResource($booking))->resolve(),
        ];

        if ($userId) {
            $userFd = $this->presenceStore->getUserFd((int) $userId);
            if ($userFd && $this->server->isEstablished($userFd)) {
                $this->server->push($userFd, json_encode($payload));
                Log::info('Sent no_driver_available status update to user socket.', [
                    'user_id' => (int) $userId,
                    'fd' => $userFd,
                    'booking_id' => $booking->id,
                ]);
            }
        }

        event(new \App\Events\BookingStatusUpdated($booking));
    }

    public function findNearbyDrivers(
        float $latitude,
        float $longitude,
        float $radiusKm = 5
    ): array {

        $nearby = [];

        foreach ($this->driverLocations as $driverId => $location) {

            $distance = $this->distance(
                $latitude,
                $longitude,
                $location['latitude'],
                $location['longitude']
            );

            if ($distance <= $radiusKm) {

                $nearby[] = [
                    'driver_id' => $driverId,
                    'distance' => $distance,
                ];
            }
        }

        usort($nearby, fn($a, $b) => $a['distance'] <=> $b['distance']);

        return $nearby;
    }

    private function distance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {

        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLng / 2) *
            sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function sendBooking(array $driverIds, array $payload): void
    {
        foreach ($driverIds as $driverId) {

            if (!isset($this->drivers[$driverId])) {
                continue;
            }

            $fd = $this->drivers[$driverId];

            $this->server->push($fd, json_encode($payload));
        }
    }

    protected function normalizeDriverIds(mixed $driverIds): array
    {
        if (! is_array($driverIds)) {
            $driverIds = [$driverIds];
        }

        $driverIds = array_map('intval', $driverIds);
        $driverIds = array_filter($driverIds, fn ($driverId) => $driverId > 0);

        return array_values(array_unique($driverIds));
    }
}
