<?php

namespace App\Services\Socket;

use OpenSwoole\WebSocket\Server;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Support\Facades\Redis;
use OpenSwoole\Core\Coroutine\go;
class SocketServer
{
    protected DriverLocationHandler $driverHandler;
    protected BookingHandler $bookingHandler;
    // protected ChatHandler $chatHandler;

    public array $drivers = [];
    public array $users = [];
    public array $connections = [];

    protected ?Server $server = null;

    public function __construct(
        DriverLocationHandler $driverHandler,
        BookingHandler $bookingHandler
        // ChatHandler $chatHandler
    ) {
        $this->driverHandler = $driverHandler;
        $this->bookingHandler = $bookingHandler;
        // $this->chatHandler = $chatHandler;
    }

    public function start(): void
    {
        $this->server = new Server('0.0.0.0', 9502);

        $this->server->on('Start', function () {
            echo "Indicab WebSocket Started\n";
        });

        $this->server->on('Open', function ($server, $request) {
            echo "Connection opened: {$request->fd}\n";

            $token = isset($request->get['token']) ? $request->get['token'] : null;

                echo "OPEN EVENT\n";

                print_r($request->get ?? []);

                echo "\n";

            if ($token) {
                if ($this->authenticateConnection($request->fd, $token)) {
                    $server->push($request->fd, json_encode([
                        'type' => 'connected',
                        'message' => 'Welcome to Indicab',
                        'authenticated' => true
                    ]));
                } else {
                    $server->push($request->fd, json_encode([
                        'type' => 'auth_error',
                        'message' => 'Invalid token'
                    ]));
                    $server->disconnect($request->fd);
                }
            } else {
                // Store initial connection without authentication
                $this->connections[$request->fd] = [
                    'fd' => $request->fd,
                    'authenticated' => false,
                    'type' => null,
                    'id' => null
                ];

                $server->push($request->fd, json_encode([
                    'type' => 'connected',
                    'message' => 'Welcome to Indicab',
                    'authenticated' => false
                ]));
            }
        });

        $this->server->on('Message', function ($server, $frame) {
            $this->handleMessage($server, $frame);
        });

        $this->server->on('Close', function ($server, $fd) {
            echo "Connection closed: {$fd}\n";
            $this->handleDisconnect($fd);
        });

        $this->server->on('Request', function ($request, $response) {

            $data = json_decode(
                $request->rawContent(),
                true
            );

            if (($request->server['request_uri'] ?? '') === '/send-booking') {

                $driverId = $data['driver_id'];

                $fd = Redis::get(
                    "driver:fd:{$driverId}"
                );

                if (
                    $fd &&
                    $this->server->isEstablished((int)$fd)
                ) {

                    $this->server->push(
                        (int)$fd,
                        json_encode($data['payload'])
                    );

                    $response->end('success');
                    return;
                }

                $response->status(404);
                $response->end('driver offline');
                return;
            }
        });
        $this->server->start();
    }
    protected function handleMessage($server, $frame): void
    {
        $payload = json_decode($frame->data, true);

        if (!isset($payload['type'])) {
            $server->disconnect($frame->fd);
            return;
        }

        // Authenticate via token if provided in payload, or require existing auth
        if (isset($payload['token'])) {
            if (!$this->authenticateConnection($frame->fd, $payload['token'])) {
                $server->push($frame->fd, json_encode([
                    'type' => 'auth_error',
                    'message' => 'Invalid token'
                ]));
                $server->disconnect($frame->fd);
                return;
            }
        } elseif (!isset($this->connections[$frame->fd]['authenticated']) || !$this->connections[$frame->fd]['authenticated']) {
            $server->push($frame->fd, json_encode([
                'type' => 'auth_error',
                'message' => 'Token required'
            ]));
            $server->disconnect($frame->fd);
            return;
        }

        // Route to appropriate handler based on user type
        switch ($payload['type']) {
            case 'authenticate':
                $server->push($frame->fd, json_encode([
                    'type' => 'authenticated',
                    'user_type' => $this->connections[$frame->fd]['type'],
                    'user_id' => $this->connections[$frame->fd]['id'],
                    'message' => "Authenticated as {$this->connections[$frame->fd]['type']}"
                ]));
                break;

            case 'driver_location':
                if ($this->connections[$frame->fd]['type'] === 'driver') {
                    $this->driverHandler->handle($server, $frame, $payload);
                }
                break;

            case 'book_ride':
            case 'accept_booking':
            case 'cancel_booking':
                $this->bookingHandler->handle($server, $frame, $payload);
                break;

            default:
                $server->push($frame->fd, json_encode([
                    'type' => 'error',
                    'message' => 'Unknown message type'
                ]));
                break;
        }
    }

    protected function authenticateConnection(int $fd, string $token): bool
    {
        // If already authenticated with the same token, no need to re-verify DB
        if (isset($this->connections[$fd]['authenticated']) &&
            $this->connections[$fd]['authenticated'] &&
            isset($this->connections[$fd]['token']) &&
            $this->connections[$fd]['token'] === $token) {
            return true;
        }

        // Find the token
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !$accessToken->tokenable) {
            return false;
        }

        // Get the tokenable model (User or Driver)
        $tokenable = $accessToken->tokenable;

        // Determine if it's a driver or user
        $isDriver = ($tokenable instanceof Driver);
        $userType = $isDriver ? 'driver' : 'user';

        // Store connection info
        $this->connections[$fd] = [
            'fd' => $fd,
            'authenticated' => true,
            'type' => $userType,
            'id' => $tokenable->id,
            'token_id' => $accessToken->id,
            'token' => $token,
            'connected_at' => time()
        ];

        // Store in appropriate array for quick lookup
        if ($isDriver) {
            $this->drivers[$tokenable->id] = $fd;

            // Store FD in Redis so the Pub/Sub subscriber process can find it
            Redis::set("driver:fd:{$tokenable->id}", $fd);

            // Initialize driver data
            $this->driverHandler->initializeDriver($tokenable->id);
        } else {
            $this->users[$tokenable->id] = $fd;

            // Store FD in Redis so the Pub/Sub subscriber process can find it
            Redis::set("user:fd:{$tokenable->id}", $fd);
        }

        echo "Authenticated: {$userType} {$tokenable->id} on FD {$fd}\n";

        return true;
    }

    protected function handleDisconnect(int $fd): void
    {
        if (!isset($this->connections[$fd])) {
            return;
        }

        $connection = $this->connections[$fd];

        if ($connection['authenticated']) {
            $userType = $connection['type'];
            $userId = $connection['id'];

            // Remove from tracking arrays
            if ($userType === 'driver') {
                unset($this->drivers[$userId]);

                Redis::del("driver:fd:{$userId}");

                // Mark driver as offline
                $this->driverHandler->markDriverOffline($userId);
            } else {
                unset($this->users[$userId]);

                Redis::del("user:fd:{$userId}");
            }

            echo "Disconnected: {$userType} {$userId}\n";
        }

        // Remove connection data
        unset($this->connections[$fd]);
    }

    // Helper methods for broadcasting
    public function broadcastToDrivers(array $data, ?array $excludeDrivers = []): void
    {
        $message = json_encode($data);
        $excludeFds = [];

        foreach ($excludeDrivers as $driverId) {
            if (isset($this->drivers[$driverId])) {
                $excludeFds[] = $this->drivers[$driverId];
            }
        }

        foreach ($this->drivers as $driverId => $fd) {
            if (!in_array($fd, $excludeFds)) {
                $this->sendToClient($fd, $message);
            }
        }
    }

    public function broadcastToUsers(array $data, ?array $excludeUsers = []): void
    {
        $message = json_encode($data);
        $excludeFds = [];

        foreach ($excludeUsers as $userId) {
            if (isset($this->users[$userId])) {
                $excludeFds[] = $this->users[$userId];
            }
        }

        foreach ($this->users as $userId => $fd) {
            if (!in_array($fd, $excludeFds)) {
                $this->sendToClient($fd, $message);
            }
        }
    }

    public function sendToDriver(int $driverId, array $data): bool
    {
        if (isset($this->drivers[$driverId])) {
            return $this->sendToClient($this->drivers[$driverId], json_encode($data));
        }
        return false;
    }

    public function sendToUser(int $userId, array $data): bool
    {
        if (isset($this->users[$userId])) {
            return $this->sendToClient($this->users[$userId], json_encode($data));
        }
        return false;
    }

    protected function sendToClient(int $fd, string $message): bool
    {
        if (isset($this->connections[$fd]) && $this->server && $this->server->isEstablished($fd)) {
            return $this->server->push($fd, $message);
        }
        return false;
    }

    // Method to get driver FD by ID
    public function getDriverFd(int $driverId): ?int
    {
        return $this->drivers[$driverId] ?? null;
    }

    // Method to get user FD by ID
    public function getUserFd(int $userId): ?int
    {
        return $this->users[$userId] ?? null;
    }
}
