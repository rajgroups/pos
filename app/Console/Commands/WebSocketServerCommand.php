<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Socket\SocketServer;

class WebSocketServerCommand extends Command
{
    protected $signature = 'socket:start';

    protected $description = 'Start OpenSwoole WebSocket Server';

    public function handle(SocketServer $socketServer)
    {
        $this->info('Starting Indicab Socket Server...');

        $socketServer->start();
    }
}
