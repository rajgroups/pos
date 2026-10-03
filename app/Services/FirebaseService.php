<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class FirebaseService
{
    protected string $databaseUrl;
    protected ?array $serviceAccount = null;

    public function __construct()
    {
        $serviceAccountFile = config('services.firebase.service_account_file', storage_path('app/firebase/firebase-service-account.json'));
        if (File::exists($serviceAccountFile)) {
            $this->serviceAccount = json_decode(File::get($serviceAccountFile), true);
        }

        $projectId = $this->serviceAccount['project_id'] ?? 'indicab-ddd95';
        $this->databaseUrl = config('services.firebase.database_url', "https://{$projectId}-default-rtdb.firebaseio.com");
    }

    /**
     * Set active ride data in Firebase RTDB
     */
    public function setActiveRide(int $bookingId, array $data): bool
    {
        if (!$this->serviceAccount) {
            Log::error('Firebase service account not found.');
            return false;
        }

        $token = $this->getOAuth2AccessToken();
        if (!$token) {
            return false;
        }

        $url = "{$this->databaseUrl}/active_rides/{$bookingId}.json";
        
        $response = Http::withToken($token)
            ->put($url, $data);

        if (!$response->successful()) {
            Log::error("Failed to set active ride in Firebase for booking {$bookingId}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Update active ride data in Firebase RTDB (Partial update)
     */
    public function updateActiveRide(int $bookingId, array $data): bool
    {
        if (!$this->serviceAccount) {
            return false;
        }

        $token = $this->getOAuth2AccessToken();
        if (!$token) {
            return false;
        }

        $url = "{$this->databaseUrl}/active_rides/{$bookingId}.json";
        
        $response = Http::withToken($token)
            ->patch($url, $data);

        if (!$response->successful()) {
            Log::error("Failed to update active ride in Firebase for booking {$bookingId}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Delete active ride data from Firebase RTDB
     */
    public function deleteActiveRide(int $bookingId): bool
    {
        if (!$this->serviceAccount) {
            return false;
        }

        $token = $this->getOAuth2AccessToken();
        if (!$token) {
            return false;
        }

        $url = "{$this->databaseUrl}/active_rides/{$bookingId}.json";
        
        $response = Http::withToken($token)
            ->delete($url);

        if (!$response->successful()) {
            Log::error("Failed to delete active ride in Firebase for booking {$bookingId}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Generate Custom Auth Token for a user or driver
     */
    public function createCustomToken(string $uid, array $claims = []): ?string
    {
        if (!$this->serviceAccount) {
            return null;
        }

        $now = time();
        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $payload = [
            'iss' => $this->serviceAccount['client_email'],
            'sub' => $this->serviceAccount['client_email'],
            'aud' => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
            'iat' => $now,
            'exp' => $now + 3600,
            'uid' => $uid,
        ];

        if (!empty($claims)) {
            $payload['claims'] = $claims;
        }

        $claimSet = $this->base64UrlEncode(json_encode($payload));

        $toSign = $header . '.' . $claimSet;
        $signature = '';

        if (!openssl_sign($toSign, $signature, $this->serviceAccount['private_key'], OPENSSL_ALGO_SHA256)) {
            Log::error('Firebase custom token generation failed: openssl_sign error');
            return null;
        }

        return $toSign . '.' . $this->base64UrlEncode($signature);
    }

    /**
     * Generate or retrieve cached Google OAuth2 Access Token using Service Account.
     */
    protected function getOAuth2AccessToken(): ?string
    {
        $cacheKey = 'firebase_database_oauth2_token_' . md5($this->serviceAccount['client_email'] ?? 'service_account');

        return Cache::remember($cacheKey, 3300, function () {
            $now = time();
            $header = $this->base64UrlEncode(json_encode([
                'alg' => 'RS256',
                'typ' => 'JWT',
            ]));

            $claimSet = $this->base64UrlEncode(json_encode([
                'iss' => $this->serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email',
                'aud' => $this->serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $toSign = $header . '.' . $claimSet;
            $signature = '';

            $privateKey = $this->serviceAccount['private_key'];
            if (!openssl_sign($toSign, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                Log::error('Firebase OAuth2 error: openssl_sign failed');
                return null;
            }

            $jwt = $toSign . '.' . $this->base64UrlEncode($signature);

            $response = Http::asForm()->post($this->serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Firebase Database OAuth2 token fetch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        });
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
