@extends('layouts.admin')
@section('title', 'Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau HP..."
            class="border border-stone-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-brown-500 bg-white w-56">
        <select name="role" class="border border-stone-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-brown-500">
            <option value="">Semua Role</option>
            <option value="user" {{ request('role')=='user'?'selected':'' }}>User</option>
            <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
        </select>
        <button type="submit" class="bg-stone-700 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-stone-800">Cari</button>
    </form>
    <button onclick="document.getElementById('add-user-modal').classList.remove('hidden')"
        class="bg-brown-700 hover:bg-brown-800 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Pengguna
    </button>
</div>

<div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-stone-50 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                <th class="text-left px-6 py-3">Pengguna</th>
                <th class="text-left px-6 py-3">Nomor HP</th>
                <th class="text-left px-6 py-3">Role</th>
                <th class="text-left px-6 py-3">Transaksi</th>
                <th class="text-left px-6 py-3">Status</th>
                <th class="text-left px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($users as $user)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-brown-100 rounded-xl flex items-center justify-center font-bold text-brown-700 text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-stone-800">{{ $user->name }}</p>
                            <p class="text-xs text-stone-400">Bergabung {{ $user->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 font-mono text-sm text-stone-700">{{ $user->phone }}</td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $user->role === 'admin' ? 'bg-brown-100 text-brown-700' : 'bg-stone-100 text-stone-600' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-stone-600">{{ $user->bookings_count ?? 0 }}</td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex gap-1.5">
                        <button onclick="toggleUser({{ $user->id }}, this)"
                            class="text-xs font-semibold {{ $user->is_active ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-green-600 bg-green-50 hover:bg-green-100' }} px-2.5 py-1.5 rounded-lg transition-colors"
                            data-active="{{ $user->is_active ? '1' : '0' }}">
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                        @if($user->id !== auth()->id())
                        <form id="del-user-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('del-user-{{ $user->id }}', 'Akun {{ addslashes($user->name) }} akan dihapus.')"
                                class="text-xs font-semibold text-red-500 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition-colors">Hapus</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>

{{-- Add User Modal --}}
<div id="add-user-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('add-user-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md z-10">
        <h3 class="font-bold text-lg text-stone-800 mb-4">Tambah Akun Baru</h3>
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full border-2 border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Nomor HP</label>
                <input type="text" name="phone" required placeholder="08xxxxxxxxxx" class="w-full border-2 border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Password</label>
                <input type="password" name="password" required class="w-full border-2 border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Role</label>
                <select name="role" class="w-full border-2 border-stone-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-brown-500 bg-white transition-all">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="flex gap-2 pt-1">
                <button type="submit" class="flex-1 bg-brown-700 hover:bg-brown-800 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">Buat Akun</button>
                <button type="button" onclick="document.getElementById('add-user-modal').classList.add('hidden')" class="flex-1 border-2 border-stone-200 text-stone-600 font-semibold py-2.5 rounded-xl text-sm hover:bg-stone-50 transition-colors">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function toggleUser(id, btn) {
    try {
        const res = await fetch(`/admin/users/${id}/toggle-active`, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
        });
        const data = await res.json();
        if (data.success) {
            Swal.fire({ icon:'success', title:'Berhasil', text: data.message, timer:1500, showConfirmButton:false })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon:'error', title:'Gagal', text: data.message });
        }
    } catch(e) {
        Swal.fire({ icon:'error', title:'Error', text:'Terjadi kesalahan.' });
    }
}
</script>
@endpush
