<?php

namespace Tests\Unit;

use App\Services\ReconPaymentService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ReconPaymentServiceTest extends TestCase
{
    public function test_build_payment_url_uses_config_and_fixed_mer_ref(): void
    {
        Config::set('payment.recon', [
            'secret' => 'test-secret-key',
            'mer_code' => 'MER001',
            'return_url' => 'https://example.com/return',
            'notify_url' => 'https://example.com/notify',
            'currency' => 'HKD',
            'language' => 'en',
            'amount' => '15000',
            'description' => 'Test payment',
            'timeout' => '20',
            'ver' => '1',
            'use_production' => false,
            'testing_url' => 'https://secure-uat.reconpayment.com/ws/b2cPay',
            'production_url' => 'https://secure.reconpayment.com/ws/b2cPay',
        ]);

        $service = new ReconPaymentService();
        $url = $service->buildPaymentUrl(['mer_ref' => 'Inv99999']);

        $this->assertStringStartsWith('https://secure-uat.reconpayment.com/ws/b2cPay?', $url);
        $this->assertStringContainsString('merRef=Inv99999', $url);
        $this->assertStringContainsString('merCode=MER001', $url);
        $this->assertStringContainsString('signType=SHA-256', $url);
        $this->assertMatchesRegularExpression('/sign=[a-f0-9]{64}/', $url);

        $expectedSign = hash('sha256', 'amt=15000&curr=HKD&desc=Test payment&lang=en&merCode=MER001&merRef=Inv99999&notifyUrl=https://example.com/notify&returnUrl=https://example.com/return&timeout=20&ver=1&test-secret-key');
        $this->assertStringContainsString('sign=' . $expectedSign, $url);
    }
}
