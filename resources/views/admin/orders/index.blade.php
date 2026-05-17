@extends('layouts.admin')
@section('title', 'Transaksi')
@section('page-title', 'Manajemen Transaksi')

@section('content')
<div class="flex flex-wrap gap-3 mb-6">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode booking, nama, atau HP..."
            class="border border-stone-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-brown-500 bg-white w-64">
        <select name="status" class="border border-stone-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-brown-500">
            <option value="">Semua Status</option>
            <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Lunas</option>
            <option value="waiting_payment" {{ request('status')=='waiting_payment'?'selected':'' }}>Menunggu Bayar</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            <option value="failed" {{ request('status')=='failed'?'selected':'' }}>Gagal</option>
            <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Kadaluarsa</option>
            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Dibatalkan</option>
        </select>
        <button type="submit" class="bg-stone-700 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-stone-800">Cari</button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-xl text-sm text-stone-500 hover:bg-stone-100 border border-stone-200">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                    <th class="text-left px-5 py-3">Booking</th>
                    <th class="text-left px-5 py-3">Pelanggan</th>
                    <th class="text-left px-5 py-3 hidden md:table-cell">Film & Jadwal</th>
                    <th class="text-left px-5 py-3">Total</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($orders as $order)
                <tr class="hover:bg-stone-50 transition-colors">
                    <td class="px-5 py-4">
                        <p class="font-mono font-bold text-brown-700 text-xs">{{ $order->booking_code }}</p>
                        <p class="text-xs text-stone-400 mt-0.5">{{ $order->created_at->format('d M Y H:i') }}</p>
                        <p class="text-xs text-stone-400">{{ $order->qty }} kursi: <span class="text-brown-600">{{ $order->seat_codes }}</span></p>
                    </td>
                    <td class="px-5 py-4">
                        <p class="font-semibold text-stone-800">{{ $order->user->name }}</p>
                        <p class="text-xs text-stone-400">{{ $order->user->phone }}</p>
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell">
                        <p class="font-medium text-stone-700 max-w-40 truncate">{{ $order->schedule->film->title }}</p>
                        <p class="text-xs text-stone-400">{{ $order->schedule->show_date->format('d M Y') }} · {{ $order->schedule->formatted_time }}</p>
                        <p class="text-xs text-stone-400 truncate max-w-40">{{ $order->schedule->cinema->name }}</p>
                    </td>
                    <td class="px-5 py-4 font-bold text-stone-800">{{ $order->formatted_total }}</td>
                    <td class="px-5 py-4">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full
                            {{ $order->status === 'paid' ? 'bg-green-100 text-green-700' :
                               ($order->status === 'waiting_payment' ? 'bg-amber-100 text-amber-700' :
                               ($order->status === 'pending' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-600')) }}">
                            {{ $order->status_label }}
                        </span>
                        @if($order->is_redeemed)
                        <span class="block text-xs text-green-600 font-semibold mt-1">✅ Redeemed</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex gap-1.5 flex-wrap">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1.5 rounded-lg transition-colors">
                                Detail
                            </a>
                            @if(in_array($order->status, ['waiting_payment','pending']))
                            <button onclick="confirmOrder({{ $order->id }}, '{{ $order->booking_code }}')"
                                class="text-xs font-semibold text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 px-2.5 py-1.5 rounded-lg transition-colors">
                                Konfirmasi
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @if($orders->isEmpty())
                <tr><td colspan="6" class="px-5 py-10 text-center text-stone-400">Tidak ada transaksi ditemukan</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection

@push('scripts')
<script>
function confirmOrder(id, code) {
    Swal.fire({
        title: 'Konfirmasi Pembayaran?',
        html: `Booking <strong>${code}</strong> akan dikonfirmasi sebagai <strong>LUNAS</strong> dan tiket akan dikirim ke pengguna.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#78716c',
        confirmButtonText: '✅ Ya, Konfirmasi!',
        cancelButtonText: 'Batal',
    }).then(async r => {
        if (!r.isConfirmed) return;
        try {
            const res = await fetch(`/admin/orders/${id}/confirm`, {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();
            Swal.fire({ icon: data.success ? 'success' : 'error', title: data.success ? 'Berhasil!' : 'Gagal', text: data.message, timer: 2500, showConfirmButton: false })
                .then(() => { if (data.success) location.reload(); });
        } catch(e) {
            Swal.fire({ icon:'error', title:'Error', text:'Terjadi kesalahan.' });
        }
    });
}
</script>
@endpush
