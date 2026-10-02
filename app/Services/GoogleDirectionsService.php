<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDirectionsService
{
    public function getDistanceMeters(float $originLat, float $originLng, float $destLat, float $destLng): ?int
    {
        $apiKey = config('services.google_maps.api_key');
        
        if (empty($apiKey)) {
            Log::warning('Google Maps API key is not configured.');
            return null;
        }

        $url = 'https://maps.googleapis.com/maps/api/directions/json';
        
        try {
            $response = Http::get($url, [
                'origin' => "{$originLat},{$originLng}",
                'destination' => "{$destLat},{$destLng}",
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (($data['status'] ?? '') === 'OK' && isset($data['routes'][0]['legs'][0]['distance']['value'])) {
                    return (int) $data['routes'][0]['legs'][0]['distance']['value'];
                }
                
                Log::error('Google Directions API returned non-OK status or missing data', ['response' => $data]);
            } else {
                Log::error('Google Directions API request failed', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Exception calling Google Directions API: ' . $e->getMessage());
        }

        return null;
    }
}
