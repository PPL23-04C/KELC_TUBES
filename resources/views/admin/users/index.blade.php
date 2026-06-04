@extends('layouts.app')

@section('title', 'Kelola Users')
@section('page-title', 'Kelola Users')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <p class="text-slate-500 text-sm">Kelola semua akun pengguna WattCare.</p>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm rounded-xl transition-all shadow-sm whitespace-nowrap">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Tambah User
        </a>
    </div>

    <div class="bg-white rounded-3xl overflow-hidden shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-5 font-semibold w-16">ID</th>
                        <th class="px-6 py-5 font-semibold">Pengguna</th>
                        <th class="px-6 py-5 font-semibold">Email</th>
                        <th class="px-6 py-5 font-semibold">Role</th>
                        <th class="px-6 py-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 text-slate-400 font-medium">#{{ $u->id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm uppercase shrink-0">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                @if($u->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg text-xs font-bold">
                                        <i data-lucide="shield-check" class="w-3 h-3"></i>
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">
                                        <i data-lucide="user" class="w-3 h-3"></i>
                                        User
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $u) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-lg transition-colors">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Hapus user ini? Tindakan ini tidak dapat dibatalkan.')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-medium text-xs rounded-lg transition-colors">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <i data-lucide="users" class="w-12 h-12 mb-3 text-slate-300"></i>
                                    <p class="font-semibold text-slate-600">Belum ada pengguna</p>
                                    <a href="{{ route('admin.users.create') }}" class="mt-3 text-blue-600 hover:underline text-sm font-medium">Tambah user baru</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-6">{{ $users->links() }}</div>
    @endif
@endsection
