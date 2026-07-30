<?php

namespace Tests\Feature;

use App\Services\FcmNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RideFcmNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_fcm_notification_service_sends_http_v1_request(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake_access_token'], 200),
            'https://fcm.googleapis.com/v1/projects/*/messages:send' => Http::response(['name' => 'projects/indicab-ddd95/messages/123'], 200),
        ]);

        $service = new FcmNotificationService();
        $result = $service->sendToToken('test_token_123', 'Test Title', 'Test Body', ['type' => 'test']);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/messages:send') &&
                $request->hasHeader('Authorization', 'Bearer fake_access_token') &&
                $request['message']['token'] == 'test_token_123' &&
                $request['message']['notification']['title'] == 'Test Title';
        });
    }

    public function test_fcm_notification_legacy_api_fallback(): void
    {
        Http::fake([
            'https://fcm.googleapis.com/fcm/send' => Http::response(['success' => 1], 200),
        ]);

        config([
            'services.fcm.service_account_file' => '/non/existent/path.json',
            'services.fcm.server_key' => 'test_server_key',
        ]);

        $service = new FcmNotificationService();
        $result = $service->sendToToken('test_token_123', 'Legacy Title', 'Legacy Body', ['type' => 'test']);

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() == 'https://fcm.googleapis.com/fcm/send' &&
                $request->hasHeader('Authorization', 'key=test_server_key') &&
                $request['to'] == 'test_token_123';
        });
    }
}
