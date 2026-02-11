<?php

return [
    'APP_NAME' => env('APP_NAME', 'LanzaTaxi'),
    'APP_ENV' => env('APP_ENV', 'local'),
    'APP_DEBUG' => env('APP_DEBUG', true),
    'APP_URL' => env('APP_URL', 'http://localhost:8000'),
    'APP_KEY' => env('APP_KEY', 'base64:' . base64_encode('LanzaTaxiSecretKey123456789')),
    'APP_CIPHER' => 'AES-256-CBC',
];
