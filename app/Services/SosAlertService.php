<?php

namespace App\Services;

use App\Models\SosAlert;

class SosAlertService
{
    public function getAllAlerts()
    {
        return SosAlert::with(['user', 'booking', 'booking.driver'])
            ->orderByRaw("FIELD(status, 'active', 'resolved')")
            ->latest()
            ->paginate(15);
    }

    public function getActiveAlerts()
    {
        return SosAlert::with(['user', 'booking', 'booking.driver'])
            ->active()
            ->latest()
            ->paginate(15);
    }

    public function getAlertById($id): ?SosAlert
    {
        return SosAlert::with(['user', 'booking', 'booking.driver', 'booking.vehicle'])
            ->findOrFail($id);
    }

    public function resolveAlert($id): bool
    {
        $alert = SosAlert::findOrFail($id);
        if ($alert->status !== 'resolved') {
            $alert->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);
            return true;
        }
        return false;
    }
}
