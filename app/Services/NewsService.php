<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsService
{
    /**
     * Keywords for the NewsAPI search query.
     * These are used to fetch articles from the API. They are broad enough
     * to get a large pool of results but focused on energy/SDG 7 topics.
     */
    private array $apiQueryKeywords = [
        'renewable energy',
        'clean energy',
        'solar energy',
        'solar panel',
        'wind energy',
        'wind turbine',
        'hydropower',
        'geothermal energy',
        'energy transition',
        'sustainable energy',
        'affordable energy',
        'energy access',
        'energi terbarukan',
        'energi bersih',
        'panel surya',
        'PLTS',
        'listrik tenaga surya',
        'transisi energi',
        'SDG 7',
    ];

    /**
     * Strict SDG 7 relevance filter keywords.
     * An article MUST contain at least one of these in its title, description,
     * or content to be displayed. These are highly specific to SDG 7 topics.
     */
    private array $sdg7FilterKeywords = [
        // English — SDG 7 core
        'renewable energy',
        'clean energy',
        'green energy',
        'sustainable energy',
        'affordable energy',
        'energy transition',
        'energy access',
        'solar energy',
        'solar panel',
        'solar power',
        'solar farm',
        'wind energy',
        'wind power',
        'wind turbine',
        'wind farm',
        'hydropower',
        'hydroelectric',
        'geothermal',
        'biomass energy',
        'biofuel',
        'energy storage',
        'battery storage',
        'power grid',
        'smart grid',
        'microgrid',
        'off-grid',
        'carbon emission',
        'carbon neutral',
        'net zero',
        'decarbonization',
        'electrification',
        'energy efficiency',
        'energy poverty',
        'sdg 7',
        'sdg7',
        // Indonesian — SDG 7 core
        'energi terbarukan',
        'energi bersih',
        'energi hijau',
        'energi berkelanjutan',
        'energi terjangkau',
        'transisi energi',
        'akses energi',
        'panel surya',
        'tenaga surya',
        'tenaga angin',
        'tenaga air',
        'pembangkit listrik',
        'listrik tenaga surya',
        'listrik tenaga angin',
        'listrik tenaga air',
        'plts',
        'pltb',
        'plta',
        'panas bumi',
        'biomassa',
        'hemat energi',
        'efisiensi energi',
        'emisi karbon',
        'energi listrik',
        'bauran energi',
    ];

    /**
     * Get SDG 7 filtered news articles.
     * Caches the results for 1 hour to prevent hitting rate limits.
     *
     * @param int $limit
     * @return array
     */
    public function getSdg7News(int $limit = 15): array
    {
        $apiKey = config('services.newsapi.key');
        $lang = config('services.newsapi.lang', '');
        $domains = config('services.newsapi.domains', '');

        $cacheKey = 'sdg7_news_articles_' . md5(($apiKey ?? 'mock') . '|' . $lang . '|' . $domains);
        
        $articles = Cache::remember($cacheKey, 3600, function () use ($limit, $apiKey) {
            if (empty($apiKey)) {
                Log::info('NewsAPI key is empty. Falling back to mock news.');
                return $this->getMockNews();
            }

            try {
                // Build API query from focused keywords
                $apiKeywords = array_map(function($kw) {
                    return Str::contains($kw, ' ') ? '"' . $kw . '"' : $kw;
                }, $this->apiQueryKeywords);
                $query = implode(' OR ', $apiKeywords);
                
                $twoWeeksAgo = now()->subWeeks(2)->format('Y-m-d');

                // Build request parameters dynamically based on config
                $params = [
                    'q' => $query,
                    'from' => $twoWeeksAgo,
                    'sortBy' => 'publishedAt',
                    'pageSize' => 100,
                    'apiKey' => $apiKey,
                ];

                if (! empty($lang)) {
                    $params['language'] = $lang;
                }

                if (! empty($domains)) {
                    // NewsAPI expects a comma-separated list of domains
                    $params['domains'] = $domains;
                }

                $response = Http::timeout(10)
                    ->get('https://newsapi.org/v2/everything', $params);

                // Fallback retry block if date query fails for any reason
                if ($response->status() === 426 || $response->status() === 400) {
                    Log::info('NewsAPI date query restricted. Retrying with default range.');

                    $retryParams = [
                        'q' => $query,
                        'sortBy' => 'publishedAt',
                        'pageSize' => 100,
                        'apiKey' => $apiKey,
                    ];
                    if (! empty($lang)) {
                        $retryParams['language'] = $lang;
                    }
                    if (! empty($domains)) {
                        $retryParams['domains'] = $domains;
                    }

                    $response = Http::timeout(10)
                        ->get('https://newsapi.org/v2/everything', $retryParams);
                }

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['status']) && $data['status'] === 'ok' && !empty($data['articles'])) {
                        $rawArticles = [];
                        foreach ($data['articles'] as $article) {
                            // Filter out removed or invalid articles
                            if (Str::contains($article['title'] ?? '', '[Removed]') || empty($article['title'])) {
                                continue;
                            }

                            $rawArticles[] = [
                                'title' => $article['title'],
                                'author' => $article['author'] ?? 'Anonim',
                                'source' => $article['source']['name'] ?? 'NewsAPI',
                                'description' => $article['description'] ?? 'Tidak ada deskripsi singkat.',
                                'content' => $article['content'] ?? $article['description'] ?? 'Detail berita tidak tersedia.',
                                'url' => $article['url'] ?? '#',
                                'urlToImage' => $article['urlToImage'] ?? 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?auto=format&fit=crop&w=800&q=80',
                                'publishedAt' => $this->formatDate($article['publishedAt'] ?? null),
                                'is_mock' => false,
                            ];
                        }
                        
                        if (!empty($rawArticles)) {
                            return $rawArticles;
                        }
                    }
                }
                
                Log::warning('NewsAPI response unsuccessful or empty. Falling back to mock news. Status code: ' . $response->status());
            } catch (\Exception $e) {
                Log::error('Error fetching news from NewsAPI: ' . $e->getMessage());
            }

            return $this->getMockNews();
        });

        // Apply strict SDG 7 post-filtering with language and relevance checks
        $filteredArticles = [];
        foreach ($articles as $article) {
            $title = $article['title'] ?? '';
            $description = $article['description'] ?? '';
            $content = $article['content'] ?? '';

            // Filter out non-Latin script articles (Arabic, Cyrillic, Chinese, Japanese, Korean, Thai, Hindi, etc.)
            if (preg_match('/[\p{Cyrillic}\p{Arabic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}\p{Thai}\p{Devanagari}]/u', $title)) {
                continue;
            }

            // Strict SDG 7 relevance check: must contain at least one specific SDG 7 phrase
            $textToSearch = Str::lower($title . ' ' . $description . ' ' . $content);

            $isRelevant = false;
            foreach ($this->sdg7FilterKeywords as $keyword) {
                if (Str::contains($textToSearch, Str::lower($keyword))) {
                    $isRelevant = true;
                    break;
                }
            }

            if ($isRelevant) {
                $article['id'] = (string) (count($filteredArticles) + 1);
                $filteredArticles[] = $article;
            }
        }

        return array_slice($filteredArticles, 0, $limit);
    }

    /**
     * Helper to format ISO dates to a clean human-readable date.
     *
     * @param string|null $dateStr
     * @return string
     */
    private function formatDate(?string $dateStr): string
    {
        if (empty($dateStr)) {
            return now()->format('d M Y');
        }

        try {
            return \Carbon\Carbon::parse($dateStr)->format('d M Y');
        } catch (\Exception $e) {
            return now()->format('d M Y');
        }
    }

    /**
     * Fallback high-quality mock news.
     *
     * @return array
     */
    public function getMockNews(): array
    {
        return [
            [
                'id' => '1',
                'title' => 'Transisi Energi Bersih: Pemerintah Dorong Pemasangan Solar Panel di Berbagai Sektor',
                'author' => 'Budi Santoso',
                'source' => 'WattCare News',
                'description' => 'Pemerintah Indonesia terus berupaya meningkatkan bauran energi terbarukan melalui gerakan nasional pemasangan solar panel di perumahan dan industri guna mencapai target SDG 7.',
                'content' => "Jakarta, WattCare – Dalam rangka mempercepat pencapaian Sustainable Development Goals (SDG) Target 7, yaitu energi bersih dan terbarukan, pemerintah Indonesia meluncurkan program akselerasi pemasangan Pembangkit Listrik Tenaga Surya (PLTS) Atap atau solar panel. Program ini menyasar sektor perumahan, industri, dan perkantoran.\n\nMenteri Energi dan Sumber Daya Mineral menyatakan bahwa penggunaan solar panel secara masif dapat menekan emisi karbon sekaligus memberikan alternatif energi listrik yang lebih bersih dan ramah lingkungan. Diharapkan langkah ini juga dapat menurunkan biaya listrik bulanan bagi masyarakat umum. Partisipasi aktif dari rumah tangga sangat dinantikan dalam transisi energi nasional ini.",
                'url' => 'https://www.esdm.go.id',
                'urlToImage' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&w=800&q=80',
                'publishedAt' => '21 Mei 2026',
                'is_mock' => true,
            ],
            [
                'id' => '2',
                'title' => 'Pemanfaatan Energi Terbarukan Berbasis Biomassa untuk Listrik Pedesaan',
                'author' => 'Siti Rahma',
                'source' => 'Energi Hijau Nusantara',
                'description' => 'Pemanfaatan limbah pertanian sebagai energi terbarukan berbasis biomassa kini mulai menerangi desa-desa terpencil di Indonesia.',
                'content' => "Sumatera, WattCare – Listrik kini bukan lagi barang mewah bagi warga di pedesaan terpencil. Melalui inisiatif pengembangan energi terbarukan berbasis biomassa, warga memanfaatkan limbah pertanian seperti sekam padi dan cangkang kelapa sawit untuk menggerakkan generator listrik.\n\nProyek percontohan ini berhasil menerangi lebih dari 500 kepala keluarga yang sebelumnya belum terjangkau oleh jaringan listrik nasional. Inisiatif ini tidak hanya menyediakan listrik murah tetapi juga mendukung ketahanan energi bersih daerah dan mengurangi limbah pertanian secara produktif.",
                'url' => 'https://www.kemendesa.go.id',
                'urlToImage' => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?auto=format&fit=crop&w=800&q=80',
                'publishedAt' => '20 Mei 2026',
                'is_mock' => true,
            ],
            [
                'id' => '3',
                'title' => 'Renewable Energy Jadi Solusi Utama Hadapi Tantangan Perubahan Iklim',
                'author' => 'John Doe',
                'source' => 'Global Eco Watch',
                'description' => 'Laporan terbaru menunjukkan bahwa percepatan adopsi renewable energy di seluruh dunia dapat menahan kenaikan suhu global di bawah 1.5 derajat Celsius.',
                'content' => "New York, WattCare – Laporan tahunan dari International Renewable Energy Agency (IRENA) menegaskan kembali bahwa percepatan transisi menuju renewable energy seperti angin, air, dan matahari merupakan satu-satunya solusi paling efektif dalam mengatasi krisis iklim global.\n\nDalam laporannya, investasi global untuk infrastruktur energi bersih harus ditingkatkan hingga tiga kali lipat pada tahun 2030 agar selaras dengan target SDG 7 dan komitmen Perjanjian Paris. Negara berkembang disarankan untuk segera memulai diversifikasi energi agar tidak ketergantungan pada bahan bakar fosil.",
                'url' => 'https://www.irena.org',
                'urlToImage' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?auto=format&fit=crop&w=800&q=80',
                'publishedAt' => '19 Mei 2026',
                'is_mock' => true,
            ],
            [
                'id' => '4',
                'title' => 'Mengapa SDG 7 Sangat Krusial Bagi Pembangunan Berkelanjutan?',
                'author' => 'Dewi Lestari',
                'source' => 'Sosial & Lingkungan',
                'description' => 'Artikel ini membahas mengapa pemenuhan target SDG 7 (Energi Bersih dan Terjangkau) menjadi pondasi utama bagi tujuan pembangunan berkelanjutan lainnya.',
                'content' => "Bandung, WattCare – SDG 7 atau Sustainable Development Goal 7 menargetkan akses terhadap energi yang terjangkau, andal, berkelanjutan, dan modern bagi semua orang. Mengapa hal ini begitu krusial? Energi merupakan faktor kunci di balik hampir setiap peluang dan tantangan besar yang dihadapi dunia saat ini.\n\nMulai dari penciptaan lapangan kerja, keamanan, perubahan iklim, produksi pangan, hingga peningkatan pendapatan, akses terhadap listrik dan energi bersih sangat menentukan keberhasilan tujuan pembangunan berkelanjutan lainnya. Tanpa energi bersih, pencapaian kesehatan dan pendidikan berkualitas akan terhambat.",
                'url' => 'https://sdgs.bappenas.go.id',
                'urlToImage' => 'https://images.unsplash.com/photo-1548613053-22087dd8edb8?auto=format&fit=crop&w=800&q=80',
                'publishedAt' => '18 Mei 2026',
                'is_mock' => true,
            ],
            [
                'id' => '5',
                'title' => 'Gaya Hidup Hemat Listrik Mulai Populer di Kalangan Generasi Muda',
                'author' => 'Rian Hidayat',
                'source' => 'Kreatif & Urban',
                'description' => 'Kampanye hemat listrik dan penggunaan perangkat elektronik hemat energi (eco-friendly) kian diminati oleh generasi milenial dan Gen Z.',
                'content' => "Jakarta, WattCare – Kesadaran lingkungan di kalangan anak muda Indonesia mengalami peningkatan signifikan. Tren terbaru menunjukkan gaya hidup hemat listrik kini dipandang sebagai bagian dari identitas modern yang ramah lingkungan.\n\nBanyak dari mereka mulai beralih menggunakan perangkat rumah tangga dengan sensor pintar (smart home devices) untuk memantau konsumsi listrik mereka secara real-time. Hal ini tidak hanya memangkas pengeluaran bulanan mereka tetapi juga membantu mengurangi beban jaringan listrik kota, sekaligus mengurangi emisi karbon tak langsung.",
                'url' => 'https://www.wattcare.id',
                'urlToImage' => 'https://images.unsplash.com/photo-1513829096960-ef0a300d8d06?auto=format&fit=crop&w=800&q=80',
                'publishedAt' => '17 Mei 2026',
                'is_mock' => true,
            ],
        ];
    }
}
