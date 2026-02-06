<?php

return [
    'allowed_mime' => array_filter(array_map(
        'trim',
        explode(',', env('IMAGES_ALLOWED_MIME', ''))
    )) ?: ['image/jpeg', 'image/png', 'image/webp'],
    'driver' => env('RESIZLY_DRIVER', 'gd'),
];
