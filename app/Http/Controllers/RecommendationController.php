<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MonitoringLog;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    // Tarif default fallback jika tidak ada di database
    private const TARIF_DEFAULT = 1444.70;

    public function index(): View
    {
        $user      = auth()->user();
        $now       = Carbon::now();
        $hasDevice = $user->devices()->exists();
        $dayaVa    = (int) $user->daya_va;
        
        // Tarif dinamis per VA user
        $tariff = getTarifListrik($dayaVa);

        // ── Data bulanan untuk banner status ─────────────────────────────
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd   = $now->copy()->endOfMonth();

        $monthUsage = MonitoringLog::where('user_id', $user->id)
            ->whereBetween('tanggal', [$monthStart, $monthEnd])
            ->sum('total_kwh');

        $thresholds = config('constants.daily_usage_thresholds');
        $resolvedTier = null;
        foreach ($thresholds as $tier) {
            if ($dayaVa <= (int) $tier['max_va']) {
                $resolvedTier = $tier;
                break;
            }
        }
        if (!$resolvedTier) {
            $resolvedTier = end($thresholds);
        }

        $hematMax = (float) $resolvedTier['hemat'] * 30;
        $sedangMax = (float) $resolvedTier['sedang'] * 30;

        if ($monthUsage < $hematMax) {
            $usageStatus = 'hemat';
        } elseif ($monthUsage <= $sedangMax) {
            $usageStatus = 'sedang';
        } else {
            $usageStatus = 'boros';
        }

        // ── Tips per device ───────────────────────────────────────────────
        $deviceTips = collect();

        if ($hasDevice) {
            $devices       = Device::where('user_id', $user->id)->get();
            $totalMonthKwh = max((float) $monthUsage, 0.01);

            $deviceData = $devices->map(function ($device) use ($user, $monthStart, $monthEnd, $totalMonthKwh, $tariff, $dayaVa) {
                $kwh = (float) MonitoringLog::where('user_id', $user->id)
                    ->where('device_id', $device->id)
                    ->whereBetween('tanggal', [$monthStart, $monthEnd])
                    ->sum('total_kwh');

                $avgJam = (float) (MonitoringLog::where('user_id', $user->id)
                    ->where('device_id', $device->id)
                    ->whereBetween('tanggal', [$monthStart, $monthEnd])
                    ->avg('jam_pemakaian') ?? 0);

                $persen       = round(($kwh / $totalMonthKwh) * 100, 1);
                $biayaBulan   = round($kwh * $tariff);

                return [
                    'device_id'   => $device->id,
                    'nama'        => $device->nama_device,
                    'daya_watt'   => $device->daya_watt,
                    'jumlah_unit' => $device->jumlah_unit,
                    'kwh_bulan'   => round($kwh, 2),
                    'avg_jam'     => round($avgJam, 1),
                    'persen'      => $persen,
                    'biaya_bulan' => $biayaBulan,
                    'tips'        => self::generateDeviceTips(
                        $device->nama_device,
                        $device->daya_watt,
                        $avgJam,
                        $kwh,
                        $persen,
                        $biayaBulan,
                        $tariff
                    ),
                ];
            })
            ->filter(fn($d) => $d['kwh_bulan'] > 0)
            ->sortByDesc('persen')
            ->values();

            $deviceTips = $deviceData;
        }

        $co2Factor = (float) config('constants.co2_factor');

        $tipChecklists = $user->recommendations()
            ->where('tipe', 'checklist')
            ->pluck('pesan')
            ->mapWithKeys(fn($item) => [$item => true])
            ->toArray();

        return view('recommendations.index', compact(
            'hasDevice',
            'monthUsage',
            'usageStatus',
            'deviceTips',
            'hematMax',
            'sedangMax',
            'tariff',
            'co2Factor',
            'dayaVa',
            'resolvedTier',
            'tipChecklists'
        ));
    }

    public function toggleTipChecklist(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'device_id' => 'required',
            'tip_index' => 'required|integer',
        ]);

        $user = auth()->user();
        $key = "{$request->device_id}_{$request->tip_index}";

        $existing = $user->recommendations()
            ->where('tipe', 'checklist')
            ->where('pesan', $key)
            ->first();

        if ($existing) {
            $existing->delete();
            $isCompleted = false;
        } else {
            $user->recommendations()->create([
                'tipe' => 'checklist',
                'pesan' => $key,
            ]);
            $isCompleted = true;
        }

        return response()->json([
            'success' => true,
            'is_completed' => $isCompleted,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helper: cocokkan nama ke kategori
    // ─────────────────────────────────────────────────────────────────────
    private static function kategori(string $namaLower): string
    {
        // AC / pendingin ruangan
        if (preg_match('/\bac\b|air\s*conditioner|pendingin|split|inverter\s*ac/i', $namaLower))
            return 'ac';

        // Kulkas / lemari es
        if (preg_match('/kulkas|lemari\s*es|refrigerator|freezer|chiller/i', $namaLower))
            return 'kulkas';

        // TV / televisi
        if (preg_match('/\btv\b|televisi|television|smart\s*tv|led\s*tv/i', $namaLower))
            return 'tv';

        // Mesin cuci
        if (preg_match('/mesin\s*cuci|washing|laundry|dryer|pengering/i', $namaLower))
            return 'mesin_cuci';

        // Pompa air
        if (preg_match('/pompa|water\s*pump|jet\s*pump|submersible/i', $namaLower))
            return 'pompa';

        // Lampu
        if (preg_match('/lampu|lamp\b|light\b|bohlam|neon|downlight|spotlight|tl\b/i', $namaLower))
            return 'lampu';

        // Komputer / PC / Laptop
        if (preg_match('/komputer|computer|\bpc\b|laptop|notebook|desktop|imac|macbook/i', $namaLower))
            return 'komputer';

        // Monitor / layar
        if (preg_match('/monitor|layar|display|screen/i', $namaLower))
            return 'monitor';

        // Kipas angin
        if (preg_match('/kipas|fan\b|exhaust|blower/i', $namaLower))
            return 'kipas';

        // Setrika
        if (preg_match('/setrika|iron\b|steam\s*iron/i', $namaLower))
            return 'setrika';

        // Pemanas air / water heater
        if (preg_match('/water\s*heater|pemanas\s*air|heater|boiler|shower\s*heater/i', $namaLower))
            return 'water_heater';

        // Dispenser air
        if (preg_match('/dispenser|galon|water\s*dispenser/i', $namaLower))
            return 'dispenser';

        // Rice cooker / magic com
        if (preg_match('/rice\s*cooker|magic\s*com|magic\s*jar|penanak|magicom/i', $namaLower))
            return 'rice_cooker';

        // Microwave / oven
        if (preg_match('/microwave|oven|microoven|toaster|air\s*fryer/i', $namaLower))
            return 'microwave';

        // Charger / adaptor
        if (preg_match('/charger|adaptor|adapter|power\s*bank|pengisi/i', $namaLower))
            return 'charger';

        // HP / smartphone
        if (preg_match('/hp\b|handphone|smartphone|ponsel|iphone|android/i', $namaLower))
            return 'hp';

        // Printer
        if (preg_match('/printer|cetak|scanner/i', $namaLower))
            return 'printer';

        // Router / modem
        if (preg_match('/router|modem|wifi|wi-fi|access\s*point/i', $namaLower))
            return 'router';

        // Speaker / audio
        if (preg_match('/speaker|audio|sound|amplifier|subwoofer/i', $namaLower))
            return 'speaker';

        // Kulkas mini
        if (preg_match('/kulkas\s*mini|mini\s*fridge|bar\s*fridge/i', $namaLower))
            return 'kulkas';

        return 'default';
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helper: format Rupiah ringkas
    // ─────────────────────────────────────────────────────────────────────
    private static function rp(float $nominal): string
    {
        if ($nominal >= 1_000_000) return 'Rp ' . number_format($nominal / 1_000_000, 1) . ' jt';
        if ($nominal >= 1_000)     return 'Rp ' . number_format($nominal / 1_000, 0, ',', '.') . ' rb';
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Main: generate tips per device
    // ─────────────────────────────────────────────────────────────────────
    private static function generateDeviceTips(
        string $nama,
        int    $watt,
        float  $avgJam,
        float  $kwh,
        float  $persen,
        int    $biayaBulan,
        float  $tariff
    ): array {
        $namaLower = strtolower($nama);
        $kat       = self::kategori($namaLower);
        $tips      = [];

        // ── estimasi hemat jika kurangi pemakaian (dipakai banyak kategori)
        $hematKurangi2Jam = round($watt * 2 * 30 / 1000, 1);
        $hematBiaya2Jam   = self::rp($hematKurangi2Jam * $tariff);

        switch ($kat) {

            // ── AC ───────────────────────────────────────────────────────
            case 'ac':
                if ($avgJam > 10) {
                    $tips[] = "AC menyala rata-rata {$avgJam} jam/hari — tergolong sangat lama. "
                            . "Aktifkan timer 1–2 jam sebelum tidur, bisa hemat ~{$hematBiaya2Jam}/bulan.";
                } elseif ($avgJam > 6) {
                    $tips[] = "AC menyala {$avgJam} jam/hari. Coba aktifkan sleep timer agar mati otomatis saat tidur.";
                }
                if ($watt > 1500) {
                    $tips[] = "AC {$watt}W termasuk daya besar. Set suhu 25–26°C — tiap 1°C lebih dingin menambah konsumsi 6–10%.";
                } elseif ($watt > 800) {
                    $tips[] = "Set suhu AC di 24–26°C untuk efisiensi optimal. Jangan set di bawah 22°C.";
                }
                if ($persen > 50) {
                    $tips[] = "AC mendominasi {$persen}% konsumsi listrikmu (Rp " . number_format($biayaBulan, 0, ',', '.') . "/bln). "
                            . "Pertimbangkan upgrade ke AC inverter — hemat 30–50% jangka panjang.";
                } elseif ($persen > 30) {
                    $tips[] = "AC menyumbang {$persen}% konsumsi bulanmu. Pastikan ruangan tertutup rapat saat AC menyala.";
                }
                $tips[] = "Bersihkan filter AC tiap 1–2 bulan — filter kotor bisa meningkatkan konsumsi hingga 15%.";
                $tips[] = "Gunakan mode 'Dry' (dehumidifier) di musim hujan — lebih hemat daripada mode 'Cool'.";
                break;

            // ── Kulkas ───────────────────────────────────────────────────
            case 'kulkas':
                $tips[] = "Jangan buka pintu kulkas terlalu sering atau terlalu lama — suhu naik tiap dibuka dan butuh energi ekstra untuk kembali dingin.";
                if ($watt > 150) {
                    $tips[] = "Kulkas {$watt}W ini cukup berdaya. Pastikan seal/karet pintu masih rapat — seal bocor bisa boros hingga 30%.";
                }
                if ($persen > 20) {
                    $tips[] = "Kulkas menyumbang {$persen}% konsumsi listrikmu. Atur suhu kulkas di 3–4°C dan freezer di -18°C — tidak perlu lebih dingin.";
                }
                $tips[] = "Jauhkan kulkas minimal 15 cm dari dinding dan jauh dari sumber panas (kompor, sinar matahari) agar kompresor tidak kerja ekstra.";
                $tips[] = "Jangan masukkan makanan/minuman panas langsung ke kulkas — tunggu hingga suhu ruangan dulu.";
                break;

            // ── TV ───────────────────────────────────────────────────────
            case 'tv':
                if ($avgJam > 8) {
                    $hematTV = round($watt * ($avgJam - 4) * 30 / 1000, 1);
                    $hematRpTV = self::rp($hematTV * $tariff);
                    $tips[] = "TV menyala rata-rata {$avgJam} jam/hari — cukup lama. "
                            . "Kurangi ke 4 jam/hari bisa hemat ~{$hematTV} kWh ({$hematRpTV}) per bulan.";
                } elseif ($avgJam > 5) {
                    $tips[] = "TV menyala {$avgJam} jam/hari. Coba pasang timer otomatis agar tidak lupa dimatikan.";
                }
                if ($watt > 150) {
                    $tips[] = "TV {$watt}W tergolong boros. Kurangi kecerahan layar 30–40% di pengaturan Picture — hemat daya tanpa mengurangi kenyamanan menonton.";
                }
                $tips[] = "Aktifkan 'Energy Saving Mode' di menu pengaturan TV — tersedia di hampir semua TV modern.";
                $tips[] = "Matikan TV benar-benar (tekan tombol power di unit), bukan hanya remote — mode standby tetap menyerap 3–8 watt.";
                if ($persen > 25) {
                    $tips[] = "TV menyumbang {$persen}% konsumsi listrik bulan ini (sekitar Rp " . number_format($biayaBulan, 0, ',', '.') . "). Batasi jam nonton agar tagihan turun.";
                }
                break;

            // ── Mesin Cuci ───────────────────────────────────────────────
            case 'mesin_cuci':
                $tips[] = "Selalu cuci dengan kapasitas penuh — mencuci setengah beban sama borosnya dengan beban penuh.";
                $tips[] = "Gunakan mode air dingin untuk cucian sehari-hari — mode air panas meningkatkan konsumsi hingga 90%.";
                if ($avgJam > 2) {
                    $tips[] = "Mesin cuci digunakan {$avgJam} jam/hari — cukup tinggi. Batasi 1 siklus per hari atau gabungkan cucian.";
                }
                if ($watt > 400) {
                    $tips[] = "Mesin cuci {$watt}W ini cukup besar. Pilih program 'Quick Wash' untuk pakaian yang tidak terlalu kotor — lebih hemat 30–40%.";
                }
                $tips[] = "Bersihkan filter mesin cuci secara rutin agar mesin bekerja efisien dan tidak boros listrik.";
                break;

            // ── Pompa Air ────────────────────────────────────────────────
            case 'pompa':
                if ($avgJam > 4) {
                    $tips[] = "Pompa air menyala {$avgJam} jam/hari — tergolong lama. Pasang tandon/toren agar pompa cukup menyala 1–2 kali sehari.";
                } elseif ($avgJam > 2) {
                    $tips[] = "Pompa menyala {$avgJam} jam/hari. Gunakan tandon air agar tidak perlu menyala terus saat penggunaan puncak.";
                }
                $tips[] = "Periksa seluruh sambungan pipa apakah ada kebocoran — kebocoran kecil pun membuat pompa bekerja terus-menerus.";
                if ($watt > 250) {
                    $tips[] = "Pompa {$watt}W ini cukup besar. Pastikan ukuran pipa sesuai spesifikasi pompa agar tidak bekerja berlebihan.";
                }
                $tips[] = "Pompa otomatis dengan pressure switch lebih hemat daripada pompa yang harus dinyalakan manual — mati sendiri saat tidak ada penggunaan.";
                break;

            // ── Lampu ────────────────────────────────────────────────────
            case 'lampu':
                if ($watt > 20) {
                    $tips[] = "Lampu {$watt}W bisa diganti lampu LED setara yang hanya butuh " . max(5, round($watt * 0.15)) . "–" . max(9, round($watt * 0.2)) . "W untuk cahaya yang sama.";
                } elseif ($watt > 10) {
                    $tips[] = "Lampu {$watt}W sudah cukup efisien. Pastikan memang sudah LED — bukan lampu neon atau pijar.";
                }
                if ($avgJam > 10) {
                    $tips[] = "Lampu menyala {$avgJam} jam/hari. Manfaatkan cahaya alami di siang hari (pukul 08.00–16.00) dan matikan lampu yang tidak dibutuhkan.";
                } elseif ($avgJam > 6) {
                    $tips[] = "Lampu menyala {$avgJam} jam/hari. Pertimbangkan pasang sensor gerak di area yang sering lupa dimatikan (toilet, lorong, teras).";
                }
                if ($persen > 20) {
                    $tips[] = "Lampu menyumbang {$persen}% konsumsi listrikmu. Ganti semua lampu ke LED sekarang — balik modal dalam 6–12 bulan.";
                }
                $tips[] = "Gunakan cat dinding warna terang (putih/krem) untuk memantulkan cahaya lebih efektif sehingga butuh lampu lebih sedikit.";
                break;

            // ── Komputer / Laptop ─────────────────────────────────────────
            case 'komputer':
                if ($avgJam > 10) {
                    $tips[] = "Komputer menyala {$avgJam} jam/hari. Matikan sepenuhnya saat selesai bekerja — jangan hanya sleep atau hibernate.";
                } elseif ($avgJam > 6) {
                    $tips[] = "Komputer menyala {$avgJam} jam/hari. Aktifkan sleep otomatis setelah 10 menit tidak aktif di pengaturan Power.";
                }
                if ($watt > 200) {
                    $tips[] = "PC desktop {$watt}W ini cukup boros. Pertimbangkan beralih ke laptop atau mini PC yang konsumsinya 3–5× lebih hemat untuk pekerjaan kantor.";
                }
                $tips[] = "Kurangi kecerahan monitor 30–50% — layar adalah komponen paling boros daya, dan mata Anda akan lebih nyaman.";
                $tips[] = "Aktifkan 'Power Saver' mode di Windows/macOS dan atur layar mati otomatis setelah 5 menit idle.";
                if ($persen > 20) {
                    $tips[] = "Komputer menyumbang {$persen}% konsumsi listrik ({$hematBiaya2Jam}/bln bisa dihemat dengan mengurangi 2 jam pemakaian).";
                }
                break;

            // ── Monitor ──────────────────────────────────────────────────
            case 'monitor':
                $tips[] = "Kurangi kecerahan monitor ke 40–60% — kebanyakan orang set terlalu terang dan ini boros daya sekaligus merusak mata.";
                if ($avgJam > 8) {
                    $tips[] = "Monitor menyala {$avgJam} jam/hari. Aktifkan fitur mati layar otomatis setelah 5 menit idle.";
                }
                if ($watt > 30) {
                    $tips[] = "Monitor {$watt}W ini cukup boros. Monitor LED IPS modern biasanya hanya 20–25W untuk ukuran yang sama.";
                }
                $tips[] = "Matikan monitor saat istirahat makan siang atau meninggalkan meja kerja — jangan biarkan screensaver menyala.";
                break;

            // ── Kipas Angin ───────────────────────────────────────────────
            case 'kipas':
                if ($avgJam > 12) {
                    $tips[] = "Kipas menyala {$avgJam} jam/hari. Gunakan timer atau smart plug agar mati otomatis saat tidur.";
                }
                if ($watt > 60) {
                    $tips[] = "Kipas {$watt}W tergolong besar. Pilih kipas DC brushless yang hanya butuh 15–25W untuk hembusan udara yang sama.";
                }
                $tips[] = "Kombinasikan kipas dengan membuka jendela di pagi/malam hari untuk sirkulasi udara alami — lebih hemat daripada AC.";
                if ($persen > 15) {
                    $tips[] = "Kipas menyumbang {$persen}% konsumsi listrik. Kipas memang lebih hemat dari AC, tapi pastikan tidak menyala di ruangan kosong.";
                }
                break;

            // ── Setrika ───────────────────────────────────────────────────
            case 'setrika':
                if ($avgJam > 1) {
                    $tips[] = "Setrika menyala {$avgJam} jam/hari — cukup lama. Kumpulkan pakaian dan setrika sekaligus 1–2× seminggu agar lebih efisien.";
                }
                if ($watt > 800) {
                    $tips[] = "Setrika {$watt}W ini berdaya besar. Gunakan hanya pada suhu yang dibutuhkan sesuai jenis kain — jangan set suhu terlalu tinggi.";
                }
                $tips[] = "Cabut setrika segera setelah selesai — setrika menyimpan panas cukup lama dan masih bisa digunakan beberapa menit setelah dicabut.";
                $tips[] = "Setrika pakaian saat masih sedikit lembap — lebih mudah rapi dan tidak perlu panas tinggi.";
                break;

            // ── Water Heater ──────────────────────────────────────────────
            case 'water_heater':
                if ($avgJam > 2) {
                    $tips[] = "Water heater menyala {$avgJam} jam/hari. Gunakan timer agar menyala 30 menit sebelum mandi dan mati setelahnya.";
                }
                if ($watt > 1000) {
                    $tips[] = "Water heater {$watt}W ini berdaya besar. Mandi 5–10 menit lebih singkat bisa hemat signifikan per bulannya.";
                }
                $tips[] = "Atur suhu water heater di 50–55°C — suhu lebih tinggi hanya memboroskan energi dan berisiko melukai kulit.";
                $tips[] = "Periksa insulasi pipa air panas — pipa yang tidak terisolasi membuang panas ke udara dan membuat heater bekerja lebih keras.";
                break;

            // ── Dispenser ─────────────────────────────────────────────────
            case 'dispenser':
                $tips[] = "Dispenser terus-menerus memanaskan dan mendinginkan air meski tidak digunakan. Matikan saat malam hari atau saat keluar rumah lama.";
                if ($watt > 400) {
                    $tips[] = "Dispenser {$watt}W ini berdaya cukup besar. Pertimbangkan dispenser tanpa fitur pendingin jika jarang butuh air dingin.";
                }
                if ($persen > 10) {
                    $tips[] = "Dispenser menyumbang {$persen}% konsumsi listrik. Gunakan termos untuk air panas agar dispenser tidak perlu terus memanaskan.";
                }
                $tips[] = "Gunakan smart plug dengan timer untuk mematikan dispenser otomatis di malam hari (misalnya pukul 23.00–05.00).";
                break;

            // ── Rice Cooker ───────────────────────────────────────────────
            case 'rice_cooker':
                $tips[] = "Setelah nasi matang, pindahkan ke wadah tertutup dan matikan rice cooker — mode 'warm' terus mengonsumsi listrik sepanjang hari.";
                if ($avgJam > 3) {
                    $tips[] = "Rice cooker menyala {$avgJam} jam/hari. Masak nasi 1–2× sehari dan matikan setelah matang untuk hemat energi.";
                }
                if ($watt > 400) {
                    $tips[] = "Rice cooker {$watt}W ini cukup besar. Masak sesuai porsi yang dibutuhkan — jangan masak sedikit tapi pakai rice cooker besar.";
                }
                $tips[] = "Rendam beras 30 menit sebelum dimasak — nasi matang lebih cepat dan butuh energi lebih sedikit.";
                break;

            // ── Microwave / Oven ──────────────────────────────────────────
            case 'microwave':
                $tips[] = "Microwave lebih hemat daripada kompor listrik untuk memanaskan makanan — gunakan microwave untuk porsi kecil.";
                if ($avgJam > 1) {
                    $tips[] = "Microwave/oven menyala {$avgJam} jam/hari — cukup lama. Manfaatkan panas sisa oven: matikan 5 menit sebelum waktu habis, panas sisa cukup untuk menyelesaikan masakan.";
                }
                if ($watt > 1000) {
                    $tips[] = "Oven/microwave {$watt}W ini berdaya besar. Hindari membuka pintu terlalu sering saat memasak — suhu turun dan butuh energi ekstra untuk naik kembali.";
                }
                $tips[] = "Gunakan wadah yang sesuai ukuran makanan — memanaskan makanan sedikit di wadah besar memboroskan energi.";
                break;

            // ── Charger ───────────────────────────────────────────────────
            case 'charger':
                $tips[] = "Cabut charger dari stop kontak segera setelah pengisian selesai — charger idle tetap menyerap 0.1–0.5 watt.";
                $tips[] = "Gunakan power strip dengan saklar — matikan satu tombol untuk memutus semua charger sekaligus saat malam hari.";
                if ($avgJam > 6) {
                    $tips[] = "Charger menyala {$avgJam} jam/hari. Hindari mengisi daya semalaman — baterai modern cukup diisi 1–2 jam.";
                }
                $tips[] = "Gunakan charger original atau berkualitas — charger murahan seringkali tidak efisien dan menghasilkan panas berlebih.";
                break;

            // ── HP / Smartphone ───────────────────────────────────────────
            case 'hp':
                $tips[] = "HP modern sangat hemat daya. Pastikan tidak mengisi daya semalaman — lepas charger saat baterai sudah penuh.";
                $tips[] = "Aktifkan 'Battery Saver' atau 'Low Power Mode' saat baterai di bawah 30% untuk memperlambat pengurasan daya.";
                if ($avgJam > 8) {
                    $tips[] = "HP digunakan {$avgJam} jam/hari — cukup lama. Kurangi kecerahan layar dan matikan fitur yang tidak dipakai (Bluetooth, GPS, NFC).";
                }
                break;

            // ── Printer ───────────────────────────────────────────────────
            case 'printer':
                $tips[] = "Matikan printer sepenuhnya saat tidak digunakan — printer inkjet/laser dalam mode standby tetap menyerap 3–10 watt.";
                if ($avgJam > 2) {
                    $tips[] = "Printer menyala {$avgJam} jam/hari. Kumpulkan dokumen yang perlu dicetak dan cetak sekaligus agar printer tidak perlu pemanasan berulang.";
                }
                $tips[] = "Gunakan mode 'Draft' atau 'Economy' untuk dokumen internal — lebih hemat tinta dan lebih cepat selesai.";
                break;

            // ── Router / Modem ────────────────────────────────────────────
            case 'router':
                $tips[] = "Router/modem menyala 24 jam biasanya mengonsumsi 5–15 watt — kecil tapi terus-menerus. Matikan saat tidur atau pergi keluar kota.";
                if ($watt > 20) {
                    $tips[] = "Router {$watt}W ini cukup besar. Pastikan ditempatkan di lokasi yang berventilasi baik agar tidak overheat dan bekerja lebih efisien.";
                }
                $tips[] = "Gunakan jadwal reboot otomatis pada router — router yang jarang direstart cenderung bekerja kurang efisien.";
                break;

            // ── Speaker / Audio ───────────────────────────────────────────
            case 'speaker':
                $tips[] = "Matikan speaker/amplifier saat tidak digunakan — banyak speaker aktif tetap mengonsumsi daya dalam mode standby.";
                if ($watt > 50) {
                    $tips[] = "Speaker/amplifier {$watt}W ini cukup berdaya. Sesuaikan volume dengan kebutuhan — volume tinggi = konsumsi daya lebih besar.";
                }
                if ($avgJam > 6) {
                    $tips[] = "Speaker menyala {$avgJam} jam/hari. Gunakan timer atau smart plug untuk mematikan otomatis saat tidak mendengarkan musik.";
                }
                break;

            // ── Default (perangkat tidak dikenali) ────────────────────────
            default:
                if ($persen > 25) {
                    $tips[] = "{$nama} menyumbang {$persen}% total konsumsi listrikmu bulan ini "
                            . "(~Rp " . number_format($biayaBulan, 0, ',', '.') . "). "
                            . "Ini cukup besar — pertimbangkan membatasi jam pemakaiannya.";
                }
                if ($avgJam > 8) {
                    $hematKwh = round($watt * 2 * 30 / 1000, 1);
                    $tips[] = "{$nama} digunakan rata-rata {$avgJam} jam/hari. "
                            . "Kurangi 2 jam saja bisa hemat ~{$hematKwh} kWh ({$hematBiaya2Jam}) per bulan.";
                } elseif ($avgJam > 4) {
                    $tips[] = "{$nama} digunakan {$avgJam} jam/hari. Pastikan dimatikan saat tidak benar-benar dibutuhkan.";
                }
                if ($watt > 500) {
                    $tips[] = "{$nama} berdaya {$watt}W — cukup besar. Gunakan hanya saat diperlukan dan hindari mode standby.";
                }
                $tips[] = "Cabut {$nama} dari stop kontak saat tidak digunakan untuk menghindari konsumsi daya hantu (phantom load).";
                break;
        }

        // ── Tips tambahan universal berdasarkan data ──────────────────────

        // Peringatan jika dominasi sangat tinggi (berlaku semua kecuali AC yang sudah ditangani)
        if ($persen > 45 && $kat !== 'ac') {
            $tips[] = "⚠️ {$nama} mendominasi {$persen}% konsumsi listrik bulan ini. "
                    . "Ini adalah prioritas utama penghematan — kurangi jam pemakaiannya segera.";
        }

        // Estimasi penghematan jika kurangi 20% pemakaian
        if ($kwh > 5 && $avgJam > 3) {
            $hematKwh20  = round($kwh * 0.2, 2);
            $hematRp20   = self::rp($hematKwh20 * $tariff);
            $tips[] = "💡 Jika pemakaian {$nama} dikurangi 20%, kamu bisa hemat ~{$hematKwh20} kWh ({$hematRp20}) bulan depan.";
        }

        return $tips;
    }
}