<?php

namespace App\Services;

class ReconPaymentService
{
    public function buildPaymentUrl(array $overrides = []): string
    {
        $config = config('payment.recon');

        $secret = $config['secret'];
        $merCode = $config['mer_code'];
        $amount = $overrides['amount'] ?? $config['amount'];
        $currency = $overrides['currency'] ?? $config['currency'];
        $description = $overrides['description'] ?? $config['description'];
        $language = $overrides['language'] ?? $config['language'];
        $notifyUrl = $overrides['notify_url'] ?? $config['notify_url'];
        $returnUrl = $overrides['return_url'] ?? $config['return_url'];
        $timeout = $overrides['timeout'] ?? $config['timeout'];
        $version = $overrides['ver'] ?? $config['ver'];
        $merRef = $overrides['mer_ref'] ?? 'Inv' . rand(1000, 100000);

        $baseUrl = $config['use_production']
            ? $config['production_url']
            : $config['testing_url'];

        $signPayload = 'amt=' . $amount
            . '&curr=' . $currency
            . '&desc=' . $description
            . '&lang=' . $language
            . '&merCode=' . $merCode
            . '&merRef=' . $merRef
            . '&notifyUrl=' . $notifyUrl
            . '&returnUrl=' . $returnUrl
            . '&timeout=' . $timeout
            . '&ver=' . $version
            . '&' . $secret;

        $sign = hash('sha256', $signPayload);

        return $baseUrl . '?amt=' . $amount
            . '&curr=' . $currency
            . '&desc=' . $description
            . '&lang=' . $language
            . '&merCode=' . $merCode
            . '&merRef=' . $merRef
            . '&notifyUrl=' . $notifyUrl
            . '&returnUrl=' . $returnUrl
            . '&sign=' . $sign
            . '&signType=SHA-256'
            . '&timeout=' . $timeout
            . '&ver=' . $version;
    }
}
