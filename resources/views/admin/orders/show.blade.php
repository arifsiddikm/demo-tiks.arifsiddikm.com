@extends('layouts.admin')
@section('title', 'Detail Order: ' . $booking->booking_code)
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.orders.index') }}" class="p-2 rounded-xl hover:bg-stone-100 text-stone-500 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="font-mono font-bold text-brown-700 text-lg">{{ $booking->booking_code }}</span>
        <span class="text-xs font-bold px-3 py-1 rounded-full
            {{ $booking->status === 'paid' ? 'bg-green-100 text-green-700' : ($booking->status === 'waiting_payment' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
            {{ $booking->status_label }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Customer Info --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5">
            <h3 class="font-bold text-stone-800 mb-4 flex items-center gap-2">👤 Data Pelanggan</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-stone-400">Nama</span><span class="font-semibold">{{ $booking->user->name }}</span></div>
                <div class="flex justify-between"><span class="text-stone-400">No. HP</span><span class="font-semibold font-mono">{{ $booking->user->phone }}</span></div>
                <div class="flex justify-between"><span class="text-stone-400">Role</span><span class="font-semibold capitalize">{{ $booking->user->role }}</span></div>
            </div>
        </div>

        {{-- Booking Info --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5">
            <h3 class="font-bold text-stone-800 mb-4 flex items-center gap-2">📋 Info Booking</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-stone-400">Kode Booking</span><span class="font-bold font-mono text-brown-700">{{ $booking->booking_code }}</span></div>
                <div class="flex justify-between"><span class="text-stone-400">Jumlah Tiket</span><span class="font-semibold">{{ $booking->qty }} tiket</span></div>
                <div class="flex justify-between"><span class="text-stone-400">Kursi</span><span class="font-bold text-brown-700">{{ $booking->seat_codes }}</span></div>
                <div class="flex justify-between"><span class="text-stone-400">Total</span><span class="font-bold text-stone-800">{{ $booking->formatted_total }}</span></div>
                <div class="flex justify-between"><span class="text-stone-400">Metode Bayar</span><span class="font-semibold capitalize">{{ $booking->payment_type ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-stone-400">Dibayar</span><span class="font-semibold">{{ $booking->paid_at?->format('d M Y H:i') ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-stone-400">Redeemed</span>
                    <span class="{{ $booking->is_redeemed ? 'text-green-600 font-bold' : 'text-stone-400' }}">
                        {{ $booking->is_redeemed ? '✅ ' . $booking->redeemed_at?->format('d M Y H:i') : 'Belum' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Film & Schedule --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 md:col-span-2">
            <h3 class="font-bold text-stone-800 mb-4 flex items-center gap-2">🎬 Film & Jadwal</h3>
            <div class="flex gap-4">
                <img src="{{ $booking->schedule->film->poster_url }}" alt="" class="w-16 h-24 object-cover rounded-xl shadow-sm flex-shrink-0">
                <div class="grid grid-cols-2 gap-3 flex-1 text-sm">
                    <div><span class="text-stone-400 text-xs block">Film</span><span class="font-bold text-stone-800">{{ $booking->schedule->film->title }}</span></div>
                    <div><span class="text-stone-400 text-xs block">Bioskop</span><span class="font-semibold">{{ $booking->schedule->cinema->name }}</span></div>
                    <div><span class="text-stone-400 text-xs block">Tanggal</span><span class="font-semibold">{{ $booking->schedule->show_date->format('d M Y') }}</span></div>
                    <div><span class="text-stone-400 text-xs block">Jam</span><span class="font-semibold">{{ $booking->schedule->formatted_time }} WIB</span></div>
                    <div><span class="text-stone-400 text-xs block">Studio</span><span class="font-semibold">{{ $booking->schedule->studio }}</span></div>
                    <div><span class="text-stone-400 text-xs block">Tipe</span><span class="font-semibold">{{ $booking->schedule->film_type }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    @if(in_array($booking->status, ['waiting_payment','pending']))
    <div class="mt-5 flex gap-3">
        <button onclick="confirmOrder({{ $booking->id }}, '{{ $booking->booking_code }}')"
            class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-xl text-sm transition-colors flex items-center gap-2">
            ✅ Konfirmasi Pembayaran Manual
        </button>
        <button onclick="cancelOrder({{ $booking->id }})"
            class="bg-red-50 hover:bg-red-100 text-red-600 font-bold px-6 py-3 rounded-xl text-sm transition-colors border border-red-200">
            ❌ Batalkan Booking
        </button>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function confirmOrder(id, code) {
    Swal.fire({ title:'Konfirmasi Pembayaran?', html:`Booking <strong>${code}</strong> akan dikonfirmasi LUNAS dan tiket dikirim ke pengguna.`,
        icon:'question', showCancelButton:true, confirmButtonColor:'#16a34a', cancelButtonColor:'#78716c',
        confirmButtonText:'✅ Ya, Konfirmasi!', cancelButtonText:'Batal'
    }).then(async r => {
        if (!r.isConfirmed) return;
        const res = await fetch(`/admin/orders/${id}/confirm`, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} });
        const data = await res.json();
        Swal.fire({ icon: data.success?'success':'error', title: data.success?'Berhasil!':'Gagal', text: data.message })
            .then(() => { if (data.success) location.reload(); });
    });
}
function cancelOrder(id) {
    Swal.fire({ title:'Batalkan Booking?', icon:'warning', showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#78716c', confirmButtonText:'Ya, Batalkan' })
    .then(async r => {
        if (!r.isConfirmed) return;
        const res = await fetch(`/admin/orders/${id}/cancel`, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'} });
        const data = await res.json();
        Swal.fire({ icon: data.success?'success':'error', title: data.message }).then(() => { if (data.success) location.reload(); });
    });
}
</script>
@endpush
