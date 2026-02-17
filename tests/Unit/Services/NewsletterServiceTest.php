<?php

namespace Tests\Unit\Services;

use App\Services\NewsletterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribe_verify_unsubscribe_and_export()
    {
        $service = $this->app->make(NewsletterService::class);

        $email = 'test@example.com';

        $res = $service->subscribe($email, '127.0.0.1');
        $this->assertTrue($res['success']);

        $verify = $service->verify($email);
        $this->assertTrue($verify['success']);

        $csv = $service->exportToCSV(true);
        $this->assertStringContainsString('Email,Subscribed At,Verified At', $csv);

        $unsubscribe = $service->unsubscribe($email);
        $this->assertTrue($unsubscribe['success']);
    }
}
