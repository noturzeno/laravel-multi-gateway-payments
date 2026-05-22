<?php

return [
    'base_uri'      => env('ECOLL_BASE_URI'),
    'hash_secret'   => env('ECOLL_HASH_SECRET'),
    'tran_type'     => env('ECOLL_TRAN_TYPE'),
    'dept_code'     => env('ECOLL_DEPT_CODE'),
    'activity_code' => env('ECOLL_MAIN_ACTIVITY_CODE'),
    'app_code'      => env('ECOLL_APP_SYSTEM_CODE'),
    'return_urls'   => [
        'success'   => env('ECOLL_RETURN_URI_SUCCESS'),
        'failed'    => env('ECOLL_RETURN_URI_FAILED'),
        'cancelled' => env('ECOLL_RETURN_URI_CANCELLED'),
    ],
];