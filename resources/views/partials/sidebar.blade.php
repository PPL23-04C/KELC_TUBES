<!-- Overlay for mobile -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false" 
     x-transition:enter="transition-opacity ease-linear duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" 
     x-transition:leave="transition-opacity ease-linear duration-300" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0" 
     class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden backdrop-blur-sm"></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 z-50 w-[260px] bg-gradient-to-b from-slate-900 to-slate-800 text-slate-300 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-2xl lg:shadow-none border-r border-slate-700/50">
    
    <!-- Header -->
    <div class="h-20 flex items-center px-6 border-b border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <i data-lucide="zap" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">WattCare</h1>
                <div class="text-[11px] text-slate-400 font-medium">Monitoring Pintar</div>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-slate-400 hover:text-white">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-8 scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-transparent">
        
        @if(auth()->check() && auth()->user()->role === 'admin')
            <!-- ADMIN MENU -->
            <div>
                <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin Menu</div>
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="users" class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Kelola Users</span>
                    </a>
                    <a href="{{ route('admin.electricity_rates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.electricity_rates.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="wallet" class="w-5 h-5 {{ request()->routeIs('admin.electricity_rates.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Tarif Listrik</span>
                    </a>
                </div>
            </div>
        @else
            <!-- USER MENU -->
            
            <!-- MENU -->
            <div>
                <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Menu</div>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>
                    <a href="{{ route('devices.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('devices.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="cpu" class="w-5 h-5 {{ request()->routeIs('devices.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Perangkat</span>
                    </a>
                    <a href="{{ route('analysis.input') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('analysis.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 {{ request()->routeIs('analysis.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Analisis</span>
                    </a>
                    <a href="{{ route('history.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('history.index') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="history" class="w-5 h-5 {{ request()->routeIs('history.index') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Riwayat</span>
                    </a>
                </div>
            </div>

            <!-- INSIGHT -->
            <div>
                <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Insight</div>
                <div class="space-y-1">
                    <a href="{{ route('saving-target.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('saving-target.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="piggy-bank" class="w-5 h-5 {{ request()->routeIs('saving-target.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Penghematan</span>
                    </a>
                    <a href="{{ route('leaderboards.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('leaderboards.index') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="trophy" class="w-5 h-5 {{ request()->routeIs('leaderboards.index') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Leaderboard</span>
                    </a>
                    <a href="{{ route('recommendations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('recommendations.index') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="lightbulb" class="w-5 h-5 {{ request()->routeIs('recommendations.index') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Rekomendasi</span>
                    </a>
                </div>
            </div>

            <!-- TOOLS -->
            <div>
                <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tools</div>
                <div class="space-y-1">
                    <a href="{{ route('reminder.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('reminder.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="bell" class="w-5 h-5 {{ request()->routeIs('reminder.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Reminder</span>
                    </a>
                    <a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('chat.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="bot" class="w-5 h-5 {{ request()->routeIs('chat.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Chat AI</span>
                    </a>
                    <a href="{{ route('news.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('news.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="newspaper" class="w-5 h-5 {{ request()->routeIs('news.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Berita</span>
                    </a>
                </div>
            </div>
            
            <!-- ACCOUNT -->
            <div>
                <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Account</div>
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-blue-600/10 text-blue-400 shadow-[inset_2px_0_0_0_#2563eb]' : 'hover:bg-slate-800 hover:text-slate-100' }}">
                        <i data-lucide="user" class="w-5 h-5 {{ request()->routeIs('profile.*') ? 'text-blue-400' : 'text-slate-400' }}"></i>
                        <span class="font-medium text-sm">Profil</span>
                    </a>
                </div>
            </div>
        @endif
    </nav>

    <!-- Footer / Logout -->
    <div class="p-4 border-t border-slate-700/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-red-400 bg-red-400/10 hover:bg-red-400/20 hover:text-red-300 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>
