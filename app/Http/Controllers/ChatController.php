<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Device;
use App\Models\MonitoringLog;
use Illuminate\Support\Carbon;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');
        $messageLower = strtolower($message);

        // Detect period context
        $periodMode = 'one_time';
        $periodMultiplier = 1;
        $periodLabel = '';

        if (preg_match('/sebulan|per bulan|perbulan|bulan/', $messageLower)) {
            $periodMode = 'month';
            $periodMultiplier = 30;
            $periodLabel = 'per bulan';
        } elseif (preg_match('/per hari|\/hari|hari\b/', $messageLower)) {
            $periodMode = 'day';
            $periodMultiplier = 1;
            $periodLabel = 'per hari';
        } elseif (preg_match('/per minggu|minggu/', $messageLower)) {
            $periodMode = 'week';
            $periodMultiplier = 4;
            $periodLabel = 'per minggu (estimasi 4 minggu -> per bulan)';
        }

        $apiKey = env('GEMINI_API_KEY');

        // Check if user is asking about saved devices
        $isSavedDeviceQuery = preg_match('/perangkat tersimpan|data perangkat|perangkat yang ada|device yang tersimpan|perangkat saya|hitungkan|hitung konsumsi|total konsumsi/i', $messageLower);

        if ($isSavedDeviceQuery && auth()->check()) {
            return $this->calculateFromSavedDevices(auth()->user(), $periodMode, $periodMultiplier, $periodLabel);
        }

        // Local fallback parser when no AI key configured
        if (!$apiKey) {
            $devices = $this->localParseDevices($messageLower);

            if (is_array($devices) && count($devices) > 0) {
                $totalKwh = 0;
                $replyText = "Berdasarkan pertanyaan Anda, berikut adalah estimasi penggunaan listrik:\n\n";

                foreach ($devices as $device) {
                    $nama = $device['nama_perangkat'] ?? 'Perangkat';
                    $jumlah = $device['jumlah'] ?? 1;
                    $daya = $device['daya_watt'] ?? null;
                    $waktu = $device['waktu_jam'] ?? null;

                    if ($daya === null || $waktu === null) {
                        return response()->json([
                            'reply' => nl2br(e("Tolong berikan detail daya (Watt) atau waktu (jam) untuk perangkat: " . ($nama ?? 'tidak diketahui') . ". Contoh: 'AC 1250W 5 jam' atau 'kulkas 160W 24 jam'."))
                        ]);
                    }

                    $baseKwh = ($jumlah * $daya * $waktu) / 1000;
                    $totalKwh += $baseKwh;
                    $replyText .= "- {$nama}: {$jumlah} unit, {$daya} Watt, selama {$waktu} jam = **" . number_format($baseKwh, 2, ',', '.') . " kWh**\n";
                }

                $replyText .= "\n**Total Konsumsi Listrik Estimasi:** **" . number_format($totalKwh, 2, ',', '.') . " kWh**";

                return response()->json([
                    'reply' => nl2br(preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', e($replyText)))
                ]);
            }

            return response()->json([
                'reply' => 'Maaf, saya belum bisa mengerti detailnya. Coba sebutkan seperti: "2 lampu 30 watt selama 3 jam" atau sertakan watt atau jumlah.'
            ]);
        }

        // If API key exists, call external model (original behavior)
        $prompt = "Anda adalah asisten pintar untuk situs WattCare (aplikasi penghitungan dan rekomendasi penggunaan listrik rumah)."
            . " Tugas utama Anda adalah MENANGANI dua jenis permintaan dari pengguna, dan selalu MERESPON HANYA DENGAN JSON VALID tanpa teks lain atau blok markdown."
            . "\n\n1) Jika pengguna menyebut perangkat listrik (mis. nama alat, jumlah unit, daya dalam Watt, durasi jam), EKSTRAK semua perangkat tersebut dan kembalikan objek JSON dengan key 'devices' yang berisi array of objects."
            . " Setiap objek perangkat harus memiliki struktur: {\"nama_perangkat\": string|null, \"jumlah\": int|null, \"daya_watt\": int|null, \"waktu_jam\": float|null}."
            . " Jangan menebak nilai yang tidak disebutkan; isi nilai yang tidak tersedia dengan null. Jika tidak ada perangkat sama sekali, kembalikan {\"devices\": []}."
            . "\n\n2) Jika permintaan pengguna bersifat umum (mis. menanyakan penjelasan, tips, perbandingan, estimasi biaya secara langsung tanpa mengirim parameter-perangkat terperinci), kembalikan objek JSON dengan key 'reply' berisi string jawaban singkat dalam Bahasa Indonesia. Anda boleh menanyakan klarifikasi jika perlu dengan menyertakan 'follow_up': true dan field 'question' berisi kalimat klarifikasi."
            . "\n\nPrinsip format: SELALU kembalikan JSON yang valid. Tidak boleh ada teks tambahan, penjelasan, atau blok markdown di luar JSON."
            . "\n\nKalimat pengguna: \"{$message}\"";

        try {
            $response = Http::timeout(60)->connectTimeout(60)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

                $aiText = preg_replace('/```json/i', '', $aiText);
                $aiText = preg_replace('/```/i', '', $aiText);
                $aiText = trim($aiText);

                $decoded = json_decode($aiText, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    if (isset($decoded['reply'])) {
                        $reply = strip_tags($decoded['reply']);
                        if (!empty($decoded['follow_up']) && !empty($decoded['question'])) {
                            $reply .= "\n\n" . $decoded['question'];
                        }

                        return response()->json([
                            'reply' => nl2br(e($reply))
                        ]);
                    }

                    if (isset($decoded['devices']) && is_array($decoded['devices'])) {
                        $devices = $decoded['devices'];
                    } else {
                        $devices = $decoded;
                    }

                    if (is_array($devices) && count($devices) > 0) {
                        $totalKwh = 0;
                        $missingInfoText = "";
                        $replyText = "Berdasarkan pertanyaan Anda, berikut adalah estimasi penggunaan listrik:\n\n";
                        $hasMissing = false;

                        foreach ($devices as $device) {
                            $nama = $device['nama_perangkat'] ?? null;
                            $jumlah = isset($device['jumlah']) ? $device['jumlah'] : null;
                            $daya = isset($device['daya_watt']) ? $device['daya_watt'] : null;
                            $waktu = isset($device['waktu_jam']) ? $device['waktu_jam'] : null;

                            $kurang = [];
                            if ($nama === null || trim($nama) === '') $kurang[] = 'nama perangkat';
                            if ($jumlah === null) $kurang[] = 'jumlah unit';
                            if ($daya === null) $kurang[] = 'daya (Watt)';
                            if ($waktu === null) $kurang[] = 'waktu (Jam)';

                            if (!empty($kurang)) {
                                $hasMissing = true;
                                $namaDisplay = $nama ? $nama : 'Perangkat yang tidak disebutkan namanya';
                                $missingInfoText .= "- Untuk **{$namaDisplay}**, informasi **" . implode(', ', $kurang) . "** belum lengkap.\n";
                            } else {
                                $baseKwh = ($jumlah * $daya * $waktu) / 1000;
                                $adjustedKwh = $baseKwh * $periodMultiplier;
                                $totalKwh += $adjustedKwh;

                                $waktuLabel = $waktu . ' jam' . ($periodLabel ? '/' . trim(str_replace('per ', '', $periodLabel)) : '');

                                if ($periodMode === 'month') {
                                    $replyText .= "- **{$nama}**: {$jumlah} unit, {$daya} Watt, selama {$waktuLabel} = **" . number_format($baseKwh, 2, ',', '.') . " kWh/hari** => **" . number_format($adjustedKwh, 2, ',', '.') . " kWh/bulan**\n";
                                } elseif ($periodMode === 'day') {
                                    $replyText .= "- **{$nama}**: {$jumlah} unit, {$daya} Watt, selama {$waktuLabel} = **" . number_format($baseKwh, 2, ',', '.') . " kWh/hari**\n";
                                } elseif ($periodMode === 'week') {
                                    $replyText .= "- **{$nama}**: {$jumlah} unit, {$daya} Watt, selama {$waktuLabel} = **" . number_format($baseKwh, 2, ',', '.') . " kWh/pekan (estimasi) => **" . number_format($adjustedKwh, 2, ',', '.') . " kWh/bulan**\n";
                                } else {
                                    $replyText .= "- **{$nama}**: {$jumlah} unit, {$daya} Watt, selama {$waktu} jam = **" . number_format($baseKwh, 2, ',', '.') . " kWh**\n";
                                }
                            }
                        }

                        if ($hasMissing) {
                            $finalReply = "Tolong lengkapi data berikut agar saya bisa menghitung estimasinya dengan akurat:\n\n" . $missingInfoText;
                            return response()->json([
                                'reply' => nl2br(preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', e($finalReply)))
                            ]);
                        }

                        $replyText .= "\n**Total Konsumsi Listrik Estimasi:** **" . number_format($totalKwh, 2, ',', '.') . " kWh**";

                        return response()->json([
                            'reply' => nl2br(preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', e($replyText)))
                        ]);
                    }
                }

                return response()->json([
                    'reply' => 'Maaf, saya tidak dapat memproses permintaan tersebut. Coba jelaskan apakah Anda ingin estimasi konsumsi (sebutkan nama alat, daya, jumlah, dan durasi) atau meminta tips hemat.'
                ]);

            } else {
                return response()->json([
                    'reply' => 'Terjadi kesalahan saat menghubungi API Gemini. ' . $response->body()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'reply' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simple local parser to extract device + hours patterns from free text.
     * Matches phrases like "ac 5 jam", "kulkas 2 jam", or "2 lampu 3 jam".
     */
    private function localParseDevices(string $text): array
    {
        $devices = [];

        // Normalize commas and conjunctions
        $clean = preg_replace('/\band\b|\batau\b/iu', ',', $text);
        $segments = preg_split('/[,;]+/', $clean);

        foreach ($segments as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            // Remove common conversational verbs to isolate device phrases.
            $segment = preg_replace('/\b(saya\s+)?(menggunakan|gunakan|pakai|pakaiannya|terpakai|dipakai)\b/iu', '', $segment);
            $segment = trim($segment);

            // Capture patterns like:
            // "2 lampu 30 watt selama 3 jam"
            // "ac 1250 watt 5 jam"
            // "ac 8 jam"
            if (preg_match('/^(?:(\d+)\s+)?([\p{L}0-9\s]+?)\s*(?:(\d+(?:[.,]\d+)?)\s*(?:w|watt))?\s*(?:selama\s*)?(\d+(?:[.,]\d+)?)\s*jam$/iu', $segment, $match)) {
                $count = isset($match[1]) && $match[1] !== '' ? intval($match[1]) : 1;
                $nameRaw = trim($match[2]);
                $power = isset($match[3]) && $match[3] !== '' ? (int) str_replace(',', '.', $match[3]) : null;
                $hours = isset($match[4]) ? (float) str_replace(',', '.', $match[4]) : null;

                $name = strtolower(trim(preg_replace('/\s+/', ' ', preg_replace('/\d+/', '', $nameRaw))));

                if ($power === null && auth()->check()) {
                    $savedDevice = Device::where('user_id', auth()->id())
                        ->whereRaw('LOWER(nama_device) LIKE ?', ['%' . $name . '%'])
                        ->first();

                    if ($savedDevice && $savedDevice->daya_watt) {
                        $power = (int) $savedDevice->daya_watt;
                    }
                }

                $devices[] = [
                    'nama_perangkat' => $name,
                    'jumlah' => $count,
                    'daya_watt' => $power,
                    'waktu_jam' => $hours,
                ];
            }
        }

        return $devices;
    }

    /**
     * Calculate electricity consumption from user's saved devices in the database.
     */
    private function calculateFromSavedDevices($user, $periodMode, $periodMultiplier, $periodLabel)
    {
        $devices = Device::where('user_id', $user->id)->get();

        if ($devices->isEmpty()) {
            return response()->json([
                'reply' => 'Anda belum memiliki perangkat yang tersimpan. Silakan tambahkan perangkat terlebih dahulu melalui menu Perangkat.'
            ]);
        }

        // Get today or current period data
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $totalKwh = 0;
        $replyText = "Berikut adalah estimasi konsumsi listrik dari perangkat Anda:\n\n";

        $tariff = getTarifListrik((int) $user->daya_va);
        $totalBiaya = 0;

        foreach ($devices as $device) {
            if ($periodMode === 'month') {
                // Get monthly usage
                $deviceKwh = (float) MonitoringLog::where('user_id', $user->id)
                    ->where('device_id', $device->id)
                    ->whereBetween('tanggal', [$monthStart, $monthEnd])
                    ->sum('total_kwh');
                $periodLabel = 'bulan ini';
            } else {
                // Get today's usage
                $deviceKwh = (float) MonitoringLog::where('user_id', $user->id)
                    ->where('device_id', $device->id)
                    ->whereDate('tanggal', $now->toDateString())
                    ->sum('total_kwh');
                $periodLabel = 'hari ini';
            }

            if ($deviceKwh > 0) {
                $totalKwh += $deviceKwh;
                $biaya = $deviceKwh * $tariff;
                $totalBiaya += $biaya;
                $replyText .= "- **{$device->nama_device}** ({$device->daya_watt}W): **" . number_format($deviceKwh, 2, ',', '.') . " kWh** (Rp " . number_format((int)$biaya, 0, ',', '.') . ")\n";
            }
        }

        if ($totalKwh > 0) {
            $replyText .= "\n---\n";
            $replyText .= "**Total Konsumsi {$periodLabel}:** **" . number_format($totalKwh, 2, ',', '.') . " kWh**\n";
            $replyText .= "**Estimasi Biaya:** Rp " . number_format((int)$totalBiaya, 0, ',', '.') . "\n";
        } else {
            $replyText = "Belum ada data konsumsi untuk periode ini. Silakan tambahkan catatan penggunaan terlebih dahulu.";
        }

        return response()->json([
            'reply' => nl2br(preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', e($replyText)))
        ]);
    }
}
