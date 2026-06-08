<?php

namespace App\Services\Socket;

class BookingHandler
{
    public function handle(
        $server,
        $frame,
        array $payload
    ): void {

        foreach ($server->connections as $fd) {

            $server->push(
                $fd,
                json_encode([
                    'type' => 'booking_status',
                    'booking_id' => $payload['booking_id'],
                    'status' => $payload['status']
                ])
            );
        }
    }
}
