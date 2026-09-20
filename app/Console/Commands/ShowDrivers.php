<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Driver;

class ShowDrivers extends Command
{
    protected $signature = 'drivers:show';

    protected $description = 'Show driver vehicle information';

    public function handle()
    {
        $drivers = Driver::with('vehicle.category.parent')->get();

        foreach ($drivers as $d) {
            $v = $d->vehicle;
            $c = $v ? $v->category : null;
            $p = $c ? $c->parent : null;

            $this->line('🚗 Driver ID: ' . $d->id);
            $this->line('👤 Name: ' . $d->name);
            $this->line('📱 Mobile: ' . $d->phone);
            $this->line('👨‍✈️ Type: ' . $d->driver_type);
            $this->line('🚘 Vehicle: ' . ($v ? $v->name : 'N/A'));
            $this->line('📂 Category: ' . ($p ? $p->name : ($c ? $c->name : 'N/A')));
            $this->line('📁 Subcategory: ' . ($p && $c ? $c->name : 'N/A'));
            $this->line('--------------------');
        }

        return Command::SUCCESS;
    }
}
