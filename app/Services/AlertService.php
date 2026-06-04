<?php

namespace App\Services;

class AlertService
{
    public function checkDailyLimit(float $todayUsage, int $dayaVa): array
    {
        $thresholds = config('constants.daily_usage_thresholds');
        $messages = config('constants.daily_alert_messages');
        $tier = $this->resolveTier($dayaVa, $thresholds);

        $hematMax = (float) $tier['hemat'];
        $sedangMax = (float) $tier['sedang'];

        $level = 'boros';
        $limit = $sedangMax;
        if ($todayUsage < $hematMax) {
            $level = 'hemat';
            $limit = $hematMax;
        } elseif ($todayUsage <= $sedangMax) {
            $level = 'sedang';
            $limit = $sedangMax;
        }

        $formatted = number_format($todayUsage, 2, '.', '');
        $limitFormatted = number_format($limit, 1, '.', '');

        $template = $messages[$level] ?? 'Penggunaan listrik hari ini: {pemakaian} kWh.';
        $message = str_replace(['{pemakaian}', '{batas}'], [$formatted, $limitFormatted], $template);

        if ($level === 'boros') {
            $recommendationUrl = route('recommendations.index');
            $message .= " Silakan cek <a href=\"{$recommendationUrl}\">rekomendasi</a> untuk menghemat penggunaan listrik Anda.";
        }

        return [
            'has_spike' => $level === 'boros',
            'level' => $level,
            'message' => $message,
        ];
    }

    private function resolveTier(int $dayaVa, array $thresholds): array
    {
        foreach ($thresholds as $tier) {
            if ($dayaVa <= (int) $tier['max_va']) {
                return $tier;
            }
        }

        return end($thresholds);
    }
}
