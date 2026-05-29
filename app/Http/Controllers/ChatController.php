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
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
            ]);
        }

        $prompt = "Tugas Anda adalah mengekstrak data perangkat listrik dari kalimat user berikut, dan mengembalikannya HANYA dalam format JSON valid (tanpa blok markdown atau teks lain). Format JSON yang diharapkan adalah array of objects dengan struktur key: 'nama_perangkat' (string), 'jumlah' (int), 'daya_watt' (int), 'waktu_jam' (float). JIKA ada informasi parameter yang TIDAK disebutkan secara spesifik oleh user, JANGAN buat estimasi, melainkan isi value parameter tersebut dengan null. Jika tidak ada perangkat listrik sama sekali di kalimat, kembalikan array kosong [].\n\nKalimat: \"{$message}\"";

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
                
                $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                
                // Clean markdown code blocks if any
                $aiText = preg_replace('/```json/i', '', $aiText);
                $aiText = preg_replace('/```/i', '', $aiText);
                $aiText = trim($aiText);

                $devices = json_decode($aiText, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($devices) && count($devices) > 0) {
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
                            $kwh = ($jumlah * $daya * $waktu) / 1000;
                            $totalKwh += $kwh;
                            $replyText .= "- **{$nama}**: {$jumlah} unit, {$daya} Watt, selama {$waktu} jam = **" . number_format($kwh, 2, ',', '.') . " kWh**\n";
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
                } else {
                    return response()->json([
                        'reply' => 'Maaf, saya hanya dapat memproses informasi mengenai konsumsi alat elektronik. Coba sebutkan nama alat, daya (watt), dan durasi (jam).'
                    ]);
                }

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
