<?php

return [
    'tariffs' => [
        450 => 415,
        900 => 1352,
        1300 => 1444.70,
        2200 => 1444.70,
        3500 => 1699.53,
        'default' => 1444.70,
    ],
    'co2_factor' => 0.89,
    'daily_usage_thresholds' => [
        'tier_1' => [
            'max_va' => 900,
            'hemat' => 1.5,
            'sedang' => 2.5,
        ],
        'tier_2' => [
            'max_va' => 2200,
            'hemat' => 2.5,
            'sedang' => 4.0,
        ],
        'tier_3' => [
            'max_va' => 5500,
            'hemat' => 4.0,
            'sedang' => 8.0,
        ],
        'tier_4' => [
            'max_va' => 999999,
            'hemat' => 7.0,
            'sedang' => 12.0,
        ],
    ],
    'daily_alert_messages' => [
        'boros' => 'PERINGATAN! Listrik BOROS: {pemakaian} kWh (batas {batas} kWh)',
        'sedang' => 'Penggunaan listrik hari ini sedang: {pemakaian} kWh.',
        'hemat' => 'Penggunaan listrik hari ini hemat: {pemakaian} kWh.',
    ],
];
