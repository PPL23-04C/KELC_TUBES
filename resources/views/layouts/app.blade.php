<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WattCare - @yield('title')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Alpine.js & Lucide -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased overflow-hidden" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-y-auto w-full">
            <!-- Topbar -->
            <header class="bg-white/80 backdrop-blur-md sticky top-0 z-10 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                        <i data-lucide="menu"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">@yield('page-title')</h2>
                        <div class="text-sm text-slate-500 hidden sm:block">Pantau penggunaan listrik Anda secara real-time.</div>
                    </div>
                </div>
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="text-sm font-medium text-slate-500 hidden sm:block">{{ now()->format('d M Y') }}</div>
                    <div>
                        @hasSection('header-actions')
                            @yield('header-actions')
                        @endif
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6 max-w-7xl mx-auto w-full">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
