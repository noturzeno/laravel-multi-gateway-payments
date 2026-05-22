<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class EcollPaymentRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('ecoll', [
            'base_uri' => 'https://ecoll.example.com/pay',
            'hash_secret' => 'test-secret',
            'tran_type' => 'TEST',
            'dept_code' => '005',
            'activity_code' => '80',
            'app_code' => 'AAAA',
            'return_urls' => [],
        ]);
    }

    public function test_redirect_requires_name_email_and_amount(): void
    {
        $response = $this->post('/ecoll/payment/redirect', []);

        $response->assertSessionHasErrors(['amount', 'name', 'email']);
    }
}
