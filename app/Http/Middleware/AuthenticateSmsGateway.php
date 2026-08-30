<?php

namespace App\Http\Middleware;

use App\Models\SmsGatewayDevice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSmsGateway
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Missing token.'
            ], 401);
        }

        $hash = hash('sha256', $token);
        $device = SmsGatewayDevice::where('token_hash', $hash)
            ->where('status', 'active')
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or inactive gateway token.'
            ], 401);
        }

        // Attach device to the request
        $request->attributes->set('sms_gateway_device', $device);

        return $next($request);
    }
}
