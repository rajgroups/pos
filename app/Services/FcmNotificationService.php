<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    protected ?string $serverKey;
    protected ?string $serviceAccountFile;

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
        $this->serviceAccountFile = config('services.fcm.service_account_file');
    }

    /**
     * Send FCM push notification to a single FCM device token.
     *
     * @param string $fcmToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (empty($fcmToken)) {
            Log::info('FCM Notification skipped: Empty FCM token');
            return false;
        }

        if ($this->hasServiceAccount()) {
            return $this->sendViaHttpV1($fcmToken, $title, $body, $data);
        }

        return $this->sendViaLegacyApi($fcmToken, $title, $body, $data);
    }

    /**
     * Send FCM push notification to multiple device tokens.
     *
     * @param array $fcmTokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToTokens(array $fcmTokens, string $title, string $body, array $data = []): bool
    {
        $validTokens = array_values(array_filter($fcmTokens, fn($token) => !empty($token)));

        if (empty($validTokens)) {
            Log::info('FCM Notification skipped: No valid FCM tokens provided');
            return false;
        }

        $allSuccessful = true;
        foreach ($validTokens as $token) {
            $success = $this->sendToToken($token, $title, $body, $data);
            if (!$success) {
                $allSuccessful = false;
            }
        }

        return $allSuccessful;
    }

    /**
     * Check if valid Firebase Service Account file exists.
     */
    protected function hasServiceAccount(): bool
    {
        return !empty($this->serviceAccountFile) && file_exists($this->serviceAccountFile);
    }

    /**
     * Send notification using FCM HTTP v1 API.
     */
    protected function sendViaHttpV1(string $fcmToken, string $title, string $body, array $data): bool
    {
        try {
            $serviceAccount = json_decode(file_get_contents($this->serviceAccountFile), true);
            if (!$serviceAccount || empty($serviceAccount['project_id'])) {
                Log::error('FCM v1 error: Invalid service account file format');
                return false;
            }

            $projectId = $serviceAccount['project_id'];
            $accessToken = $this->getOAuth2AccessToken($serviceAccount);

            if (empty($accessToken)) {
                Log::error('FCM v1 error: Failed to generate OAuth2 Access Token');
                return false;
            }

            // Convert data array values to strings for FCM HTTP v1
            $stringData = ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'];
            foreach ($data as $key => $val) {
                $stringData[(string)$key] = (string)$val;
            }
            $stringData['title'] = $title;
            $stringData['body'] = $body;

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $stringData,
                    'android' => [
                        'priority' => 'HIGH',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'high_importance_channel',
                        ],
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            Log::info('FCM HTTP v1 Response', [
                'token' => substr($fcmToken, 0, 15) . '...',
                'title' => $title,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('FCM HTTP v1 exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification using FCM Legacy API.
     */
    protected function sendViaLegacyApi(string $fcmToken, string $title, string $body, array $data): bool
    {
        if (empty($this->serverKey)) {
            Log::warning('FCM Notification skipped: Neither FCM_SERVER_KEY nor service account is configured');
            return false;
        }

        try {
            $payload = [
                'to' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
                'data' => array_merge([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'title' => $title,
                    'body' => $body,
                ], $data),
                'priority' => 'high',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            Log::info('FCM Legacy API Response', [
                'token' => substr($fcmToken, 0, 15) . '...',
                'title' => $title,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('FCM Legacy API exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate or retrieve cached Google OAuth2 Access Token using Service Account.
     */
    protected function getOAuth2AccessToken(array $serviceAccount): ?string
    {
        $cacheKey = 'firebase_fcm_oauth2_token_' . md5($serviceAccount['client_email'] ?? 'service_account');

        return Cache::remember($cacheKey, 3300, function () use ($serviceAccount) {
            $now = time();
            $header = $this->base64UrlEncode(json_encode([
                'alg' => 'RS256',
                'typ' => 'JWT',
            ]));

            $claimSet = $this->base64UrlEncode(json_encode([
                'iss' => $serviceAccount['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $toSign = $header . '.' . $claimSet;
            $signature = '';

            $privateKey = $serviceAccount['private_key'];
            if (!openssl_sign($toSign, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                Log::error('FCM OAuth2 error: openssl_sign failed');
                return null;
            }

            $jwt = $toSign . '.' . $this->base64UrlEncode($signature);

            $response = Http::asForm()->post($serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('FCM OAuth2 token fetch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        });
    }

    /**
     * Base64URL encode string.
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
