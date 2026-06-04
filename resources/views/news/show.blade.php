@extends('layouts.app')

@section('title', $article['title'])
@section('page-title', 'Detail Berita SDG 7')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-900 font-medium text-sm mb-6 transition-colors group">
        <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center group-hover:bg-slate-50 transition-colors shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </div>
        Kembali ke Berita
    </a>

    <article class="bg-white rounded-3xl overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.05)] border border-slate-100">
        <!-- Featured Image -->
        @if(!empty($article['urlToImage']))
        <div class="relative h-72 md:h-96 overflow-hidden">
            <img src="{{ $article['urlToImage'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-6">
                <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-md text-white border border-white/20 text-xs font-bold px-3 py-1.5 rounded-full">
                    <i data-lucide="globe" class="w-3 h-3"></i>
                    {{ $article['source'] }}
                </span>
            </div>
        </div>
        @endif

        <!-- Header -->
        <div class="p-6 md:p-8">
            <header class="mb-6">
                @if(empty($article['urlToImage']))
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-100 text-xs font-bold px-3 py-1.5 rounded-full mb-4">
                        <i data-lucide="globe" class="w-3 h-3"></i>
                        {{ $article['source'] }}
                    </span>
                @endif

                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 leading-tight mb-4">
                    {{ $article['title'] }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500 pb-5 border-b border-slate-100">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        {{ $article['author'] }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        {{ $article['publishedAt'] }}
                    </span>
                </div>
            </header>

            <!-- Content -->
            <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-base space-y-4">
                {!! nl2br(e($article['content'])) !!}
            </div>

            <!-- Footer / Original Source Link -->
            @if(!empty($article['url']) && $article['url'] !== '#')
                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">Ingin membaca artikel lengkap dari sumber aslinya?</p>
                    <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm rounded-xl transition-all shadow-sm whitespace-nowrap">
                        Baca Sumber Asli
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                    </a>
                </div>
            @endif
        </div>
    </article>
</div>
@endsection
