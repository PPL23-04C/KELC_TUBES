<?php

return [
    'co2_factor' => 0.89,
'daily_usage_thresholds' => [
    'tier_1' => [
        'max_va' => 450,
        'hemat' => 2.0,
        'sedang' => 4.0,
    ],

    'tier_2' => [
        'max_va' => 900,
        'hemat' => 3.0,
        'sedang' => 5.0,
    ],

    'tier_3' => [
        'max_va' => 1300,
        'hemat' => 4.0,
        'sedang' => 7.0,
    ],

    'tier_4' => [
        'max_va' => 2200,
        'hemat' => 6.0,
        'sedang' => 10.0,
    ],

    'tier_5' => [
        'max_va' => 999999,
        'hemat' => 10.0,
        'sedang' => 15.0,
    ],
    ],
    'daily_alert_messages' => [
        'boros' => 'PERINGATAN! Listrik BOROS: {pemakaian} kWh (batas {batas} kWh)',
        'sedang' => 'Penggunaan listrik hari ini sedang: {pemakaian} kWh.',
        'hemat' => 'Penggunaan listrik hari ini hemat: {pemakaian} kWh.',
    ],
];
