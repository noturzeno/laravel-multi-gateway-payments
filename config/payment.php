<?php

return [
    'recon' => [
        'secret' => env('RECON_SECRET_KEY', ''),
        'mer_code' => env('RECON_MERCHANT_CODE', ''),
        'return_url' => env('RECON_RETURN_URL', ''),
        'notify_url' => env('RECON_NOTIFY_URL', ''),
        'currency' => env('RECON_CURRENCY', 'HKD'),
        'language' => env('RECON_LANGUAGE', 'en'),
        'amount' => env('RECON_AMOUNT', '15000'),
        'description' => env('RECON_DESCRIPTION', 'This is a 24 monthsIPPexample.'),
        'timeout' => env('RECON_TIMEOUT', '20'),
        'ver' => env('RECON_VERSION', '1'),
        'use_production' => env('RECON_USE_PRODUCTION', false),
        'testing_url' => env('RECON_TESTING_URL', 'https://secure-uat.reconpayment.com/ws/b2cPay'),
        'production_url' => env('RECON_PRODUCTION_URL', 'https://secure.reconpayment.com/ws/b2cPay'),
    ],
];
