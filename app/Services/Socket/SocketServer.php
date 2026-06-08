<?php

namespace App\Services\Socket;

use OpenSwoole\WebSocket\Server;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use App\Models\Driver;

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

            // Store initial connection without authentication
            $this->connections[$request->fd] = [
                'fd' => $request->fd,
                'authenticated' => false,
                'type' => null,
                'id' => null
            ];

            $server->push(
                $request->fd,
                json_encode([
                    'type' => 'connected',
                    'message' => 'Welcome to Indicab'
                ])
            );
        });

        $this->server->on('Message', function ($server, $frame) {
            $this->handleMessage($server, $frame);
        });

        $this->server->on('Close', function ($server, $fd) {
            echo "Connection closed: {$fd}\n";
            $this->handleDisconnect($fd);
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

        // Handle authentication first
        if ($payload['type'] === 'authenticate') {
            $this->handleAuthentication($server, $frame, $payload);
            return;
        }

        // Check if connection is authenticated
        if (!isset($this->connections[$frame->fd]['authenticated']) ||
            !$this->connections[$frame->fd]['authenticated']) {
            $server->disconnect($frame->fd);
            return;
        }

        // Route to appropriate handler based on user type
        switch ($payload['type']) {
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

    protected function handleAuthentication($server, $frame, array $payload): void
    {
        if (!isset($payload['token'])) {
            $server->push($frame->fd, json_encode([
                'type' => 'auth_error',
                'message' => 'Token required'
            ]));
            $server->disconnect($frame->fd);
            return;
        }

        // Find the token
        $accessToken = PersonalAccessToken::findToken($payload['token']);

        if (!$accessToken) {
            $server->push($frame->fd, json_encode([
                'type' => 'auth_error',
                'message' => 'Invalid token'
            ]));
            $server->disconnect($frame->fd);
            return;
        }

        // Get the tokenable model (User or Driver)
        $tokenable = $accessToken->tokenable;

        // Determine if it's a driver or user
        $isDriver = ($tokenable instanceof Driver);
        $userType = $isDriver ? 'driver' : 'user';

        // Store connection info
        $this->connections[$frame->fd] = [
            'fd' => $frame->fd,
            'authenticated' => true,
            'type' => $userType,
            'id' => $tokenable->id,
            'token_id' => $accessToken->id,
            'connected_at' => time()
        ];

        // Store in appropriate array for quick lookup
        if ($isDriver) {
            $this->drivers[$tokenable->id] = $frame->fd;

            // Initialize driver data
            $this->driverHandler->initializeDriver($tokenable->id);
        } else {
            $this->users[$tokenable->id] = $frame->fd;
        }

        // Send success response
        $server->push($frame->fd, json_encode([
            'type' => 'authenticated',
            'user_type' => $userType,
            'user_id' => $tokenable->id,
            'message' => "Authenticated as {$userType}"
        ]));

        echo "Authenticated: {$userType} {$tokenable->id} on FD {$frame->fd}\n";
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

                // Mark driver as offline
                $this->driverHandler->markDriverOffline($userId);
            } else {
                unset($this->users[$userId]);
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
