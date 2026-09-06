<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;

class RideController extends Controller
{
    private function getRides(Request $request, $statusGroup)
    {
        $query = Booking::with(['user', 'driver', 'fare', 'pickupLocation', 'dropLocation']);
        
        if (is_array($statusGroup)) {
            $query->whereIn('status', $statusGroup);
        } else {
            $query->where('status', $statusGroup);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('mobile', 'like', "%{$search}%");
                  })
                  ->orWhereHas('driver', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        return $query->latest()->paginate(15);
    }

    public function upcoming(Request $request)
    {
        $rides = $this->getRides($request, [
            Booking::STATUS_PENDING, 
            Booking::STATUS_REQUESTED, 
            Booking::STATUS_SCHEDULED, 
            Booking::STATUS_SEARCHING_DRIVER
        ]);
        $type = 'Upcoming';
        return view('admin.ride.list', compact('rides', 'type'));
    }

    public function active(Request $request)
    {
        $rides = $this->getRides($request, [
            Booking::STATUS_ASSIGNED, 
            Booking::STATUS_DISPATCHED, 
            Booking::STATUS_ACCEPTED, 
            Booking::STATUS_ARRIVED, 
            Booking::STATUS_STARTED, 
            Booking::STATUS_IN_PROGRESS
        ]);
        $type = 'Active';
        return view('admin.ride.list', compact('rides', 'type'));
    }

    public function cancelled(Request $request)
    {
        $rides = $this->getRides($request, [Booking::STATUS_CANCELLED, Booking::STATUS_EXPIRED, Booking::STATUS_TIMEOUT, Booking::STATUS_NO_DRIVER_AVAILABLE]);
        $type = 'Cancelled';
        return view('admin.ride.list', compact('rides', 'type'));
    }

    public function completed(Request $request)
    {
        $rides = $this->getRides($request, Booking::STATUS_COMPLETED);
        $type = 'Completed';
        return view('admin.ride.list', compact('rides', 'type'));
    }

    public function show($id)
    {
        $booking = Booking::with([
            'user', 
            'driver', 
            'vehicle', 
            'category', 
            'pickupLocation', 
            'dropLocation', 
            'usage', 
            'fare',
            'sosAlerts'
        ])->findOrFail($id);

        $drivers = Driver::where('status', 'active')->where('is_online', true)->with('vehicle.location')->get();

        $pickupLat = $booking->pickupLocation?->latitude;
        $pickupLng = $booking->pickupLocation?->longitude;
        
        foreach($drivers as $driver) {
            $driverLat = $driver->vehicle?->location?->latitude;
            $driverLng = $driver->vehicle?->location?->longitude;
            $driver->calculated_distance = $this->calculateDistance($pickupLat, $pickupLng, $driverLat, $driverLng);
        }

        $drivers = $drivers->sortBy(function($driver) {
            return $driver->calculated_distance ?? 999999;
        });

        return view('admin.ride.show', compact('booking', 'drivers'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return null;
        
        $earthRadius = 6371; // km
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
          
        return $angle * $earthRadius;
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id'
        ]);

        $booking = Booking::findOrFail($id);
        
        $vehicleId = Vehicle::where('driver_id', $request->driver_id)
            ->where('status', 'active')
            ->orderByDesc('id')
            ->value('id');

        $booking->update([
            'driver_id' => $request->driver_id,
            'vehicle_id' => $vehicleId,
            'status' => Booking::STATUS_ASSIGNED,
        ]);
        
        $booking->loadMissing(['user', 'driver', 'vehicle', 'category', 'pickupLocation', 'dropLocation']);
        app(\App\Services\BookingService::class)->broadcastBookingUpdate($booking);

        return redirect()->back()->with('success', 'Driver assigned successfully.');
    }

    public function completeRide(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        try {
            app(\App\Services\BookingService::class)->completeBooking($booking, [], true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Ride manually marked as completed.');
    }

    public function cancelRide(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        try {
            app(\App\Services\BookingService::class)->cancelBooking($booking, true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Ride successfully cancelled.');
    }
}
