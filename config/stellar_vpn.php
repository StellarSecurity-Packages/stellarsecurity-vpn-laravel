<?php

return [
    'base_url' => env('STELLAR_VPN_BASE_URL', 'https://stellarvpnapiprod.azurewebsites.net/api'),
    'username' => env('STELLAR_VPN_USERNAME'),
    'password' => env('STELLAR_VPN_PASSWORD'),
    'timeout'  => env('STELLAR_VPN_TIMEOUT', 10),
];
