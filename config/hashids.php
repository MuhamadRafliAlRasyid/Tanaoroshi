<?php

return [
    'default' => 'main',

    'connections' => [
        'main' => [
            'salt' => env('HASHIDS_SALT', 'rahasia-unik-untuk-aplikasi-kamu-2025'),
            'length' => 6, // Panjang 6 untuk keseimbangan unik dan pendek
            'alphabet' => 'abcdefghijklmnopqrstuvwxyz1234567890',
        ],
        'alternative' => [
            'salt' => env('HASHIDS_SALT_ALTERNATIVE', 'rahasia-lain-untuk-alternatif'),
            'length' => 8,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyz1234567890',
        ],
    ],

];
