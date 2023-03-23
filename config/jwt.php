<?php

return [
    'private_key' => env('JWT_PRIVATE_KEY', storage_path('jwt-private.key')),

    'public_key' => env('JWT_PUBLIC_KEY', storage_path('jwt-public.key')),

    'public_key_passphrase' => env('JWT_PUBLIC_KEY_PASSPHRASE', ''),

    'private_key_passphrase' => env('JWT_PRIVATE_KEY_PASSPHRASE', ''),

    'expiry_seconds' => env('JWT_EXPIRY_SECONDS', 3600)
];
