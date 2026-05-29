@extends('layouts.app')

@section('title', 'Kelola Users')
@section('page-title', 'Kelola Users')

@section('content')
    <div class="device-header">
        <div class="meta">Kelola semua akun pengguna WattCare</div>
        <a href="{{ route('admin.users.create') }}" class="btn">+ Tambah User</a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="chip" style="position:static; background: {{ $u->role === 'admin' ? 'linear-gradient(135deg, #dbeafe, #bfdbfe)' : 'linear-gradient(135deg, #e8eef7, #dfe7f2)' }}; color: {{ $u->role === 'admin' ? '#1e40af' : 'var(--muted)' }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td class="action-cell">
                            <a class="btn btn-sm secondary" href="{{ route('admin.users.edit', $u) }}">Edit</a>
                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm danger" type="submit" onclick="return confirm('Hapus user ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">Belum ada user. <a href="{{ route('admin.users.create') }}">Tambah user baru</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div style="margin-top: 20px;">{{ $users->links() }}</div>
    @endif
@endsection
