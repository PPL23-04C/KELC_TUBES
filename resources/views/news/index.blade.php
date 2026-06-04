@extends('layouts.app')

@section('title', 'Berita SDG 7')
@section('page-title', 'Berita SDG 7: Energi Bersih & Terjangkau')

@section('content')
    @if(empty(config('services.newsapi.key')))
        <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 mb-8">
            <i data-lucide="info" class="w-5 h-5 text-blue-500 mt-0.5 shrink-0"></i>
            <p class="text-sm text-blue-700 leading-relaxed">
                <strong>Mode Demo:</strong> Menampilkan berita simulasi SDG 7. Untuk mendapatkan berita terkini secara real-time dari ribuan media, silakan masukkan <code class="bg-blue-100 px-1 rounded text-xs">NEWS_API_KEY</code> pada file <code class="bg-blue-100 px-1 rounded text-xs">.env</code> Anda.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($articles as $article)
            <article class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 flex flex-col">
                <div class="relative h-48 bg-slate-100 overflow-hidden">
                    @if(!empty($article['urlToImage']))
                        <img src="{{ $article['urlToImage'] }}" alt="{{ $article['title'] }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                            <i data-lucide="newspaper" class="w-12 h-12 text-slate-400"></i>
                        </div>
                    @endif
                    <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-slate-700 text-xs font-bold px-3 py-1 rounded-full border border-slate-200/60 shadow-sm">
                        {{ $article['source'] }}
                    </span>
                </div>

                <div class="flex flex-col flex-1 p-5">
                    <div class="flex items-center gap-3 text-xs text-slate-400 mb-3">
                        <span class="flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            {{ $article['publishedAt'] }}
                        </span>
                        @if(!empty($article['author']))
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span class="truncate">{{ $article['author'] }}</span>
                        @endif
                    </div>

                    <h3 class="font-bold text-slate-900 leading-snug mb-3 line-clamp-3 flex-1">
                        <a href="{{ route('news.show', $article['id']) }}" class="hover:text-blue-600 transition-colors">
                            {{ $article['title'] }}
                        </a>
                    </h3>

                    <p class="text-sm text-slate-500 leading-relaxed mb-4 line-clamp-2">
                        {{ Str::limit($article['description'], 120) }}
                    </p>

                    <a href="{{ route('news.show', $article['id']) }}"
                       class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold text-sm transition-colors mt-auto group">
                        Baca Selengkapnya
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center bg-slate-50 border border-slate-200 border-dashed rounded-3xl p-16 text-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-4 text-slate-400 shadow-sm">
                    <i data-lucide="newspaper" class="w-10 h-10"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak Ada Berita</h3>
                <p class="text-sm text-slate-500 max-w-xs">Belum ada berita mengenai SDG 7 saat ini. Silakan periksa kembali nanti.</p>
            </div>
        @endforelse
    </div>
@endsection
