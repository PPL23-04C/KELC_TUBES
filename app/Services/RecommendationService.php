<?php

namespace App\Services;

class RecommendationService
{
    public function buildRecommendations(float $weeklyUsage, float $weeklyAverage): array
    {
        $tips = [
            'Matikan perangkat elektronik yang tidak digunakan.',
            'Gunakan perangkat hemat energi dengan label efisiensi tinggi.',
            'Optimalkan penggunaan AC dan atur suhu pada 24-26 derajat Celsius.',
            'Manfaatkan pencahayaan alami pada siang hari.',
        ];

        if ($weeklyAverage > 0 && $weeklyUsage > $weeklyAverage * 1.2) {
            array_unshift($tips, 'Penggunaan minggu ini tinggi, prioritaskan perangkat penting saja.');
        }

        return $tips;
    }
}
