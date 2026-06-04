<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

        // Detect time context from user message to compute daily/monthly estimates
        $periodMode = 'one_time'; // one_time | day | week | month
        $periodMultiplier = 1; // multiplier to convert base (hours) to desired period
        $periodLabel = '';

        if (preg_match('/sebulan|per bulan|perbulan|bulan/', $messageLower)) {
            $periodMode = 'month';
            $periodMultiplier = 30; // days per month estimate
            $periodLabel = 'per bulan';
        } elseif (preg_match('/per hari|\/hari|hari\b/', $messageLower)) {
            $periodMode = 'day';
            $periodMultiplier = 1;
            $periodLabel = 'per hari';
        } elseif (preg_match('/per minggu|minggu/', $messageLower)) {
            $periodMode = 'week';
            $periodMultiplier = 4; // approx 4 weeks -> monthly multiplier
            $periodLabel = 'per minggu (estimasi 4 minggu -> per bulan)';
        }
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
            ]);
        }

        $prompt = "Anda adalah asisten pintar untuk situs WattCare (aplikasi penghitungan dan rekomendasi penggunaan listrik rumah)."
            . " Tugas utama Anda adalah MENANGANI dua jenis permintaan dari pengguna, dan selalu MERESPON HANYA DENGAN JSON VALID tanpa teks lain atau blok markdown."
            . "\n\n1) Jika pengguna menyebut perangkat listrik (mis. nama alat, jumlah unit, daya dalam Watt, durasi jam), EKSTRAK semua perangkat tersebut dan kembalikan objek JSON dengan key 'devices' yang berisi array of objects."
            . " Setiap objek perangkat harus memiliki struktur: {\"nama_perangkat\": string|null, \"jumlah\": int|null, \"daya_watt\": int|null, \"waktu_jam\": float|null}."
            . " Jangan menebak nilai yang tidak disebutkan; isi nilai yang tidak tersedia dengan null. Jika tidak ada perangkat sama sekali, kembalikan {\"devices\": []}."
            . "\n\n2) Jika permintaan pengguna bersifat umum (mis. menanyakan penjelasan, tips, perbandingan, estimasi biaya secara langsung tanpa mengirim parameter-perangkat terperinci), kembalikan objek JSON dengan key 'reply' berisi string jawaban singkat dalam Bahasa Indonesia. Anda boleh menanyakan klarifikasi jika perlu dengan menyertakan 'follow_up': true dan field 'question' berisi kalimat klarifikasi. Contoh: {\"reply\": \"Jawaban singkat...\", \"follow_up\": true, \"question\": \"Apakah Anda ingin estimasi biaya untuk semua perangkat atau per peralatan?\"}"
            . "\n\nPrinsip format: SELALU kembalikan JSON yang valid. Tidak boleh ada teks tambahan, penjelasan, atau blok markdown di luar JSON."
            . "\n\nBerikan jawaban yang relevan dan singkat berdasarkan konteks WattCare (konsumsi listrik, kWh, VA, rekomendasi penghematan, estimasi biaya jika data lengkap)."
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

                // Clean markdown code blocks if any
                $aiText = preg_replace('/```json/i', '', $aiText);
                $aiText = preg_replace('/```/i', '', $aiText);
                $aiText = trim($aiText);

                $decoded = json_decode($aiText, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Case A: model returned a reply object
                    if (isset($decoded['reply'])) {
                        $reply = strip_tags($decoded['reply']);
                        // If model asked for follow-up, include question
                        if (!empty($decoded['follow_up']) && !empty($decoded['question'])) {
                            $reply .= "\n\n" . $decoded['question'];
                        }

                        return response()->json([
                            'reply' => nl2br(e($reply))
                        ]);
                    }

                    // Case B: model returned devices in new format
                    if (isset($decoded['devices']) && is_array($decoded['devices'])) {
                        $devices = $decoded['devices'];
                    } else {
                        // Backwards compatibility: model returned array of devices directly
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
                                // Base kWh for the provided waktu (interpreted as hours per usage or per day depending on message)
                                $baseKwh = ($jumlah * $daya * $waktu) / 1000; // in kWh

                                // Adjust by detected period multiplier (e.g., convert daily to monthly)
                                $adjustedKwh = $baseKwh * $periodMultiplier;
                                $totalKwh += $adjustedKwh;

                                // Prepare human-friendly labels
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

                // If we reach here, fallback to generic help message
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
}
