<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model {
    protected $fillable = [
        'booking_code','user_id','schedule_id','qty','total_price',
        'status','snap_token','midtrans_order_id','payment_type',
        'paid_at','expired_at','is_redeemed','redeemed_at'
    ];
    protected $casts = [
        'paid_at' => 'datetime', 'expired_at' => 'datetime',
        'redeemed_at' => 'datetime', 'is_redeemed' => 'boolean',
        'total_price' => 'decimal:2',
    ];

    public function user()         { return $this->belongsTo(User::class); }
    public function schedule()     { return $this->belongsTo(FilmSchedule::class, 'schedule_id'); }
    public function bookingSeats() { return $this->hasMany(BookingSeat::class); }

    public function getFormattedTotalAttribute() {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusLabelAttribute() {
        return match($this->status) {
            'pending'         => 'Menunggu',
            'waiting_payment' => 'Menunggu Pembayaran',
            'paid'            => 'Lunas',
            'failed'          => 'Gagal',
            'expired'         => 'Kadaluarsa',
            'cancelled'       => 'Dibatalkan',
            default           => ucfirst($this->status),
        };
    }

    public function getSeatCodesAttribute() {
        return $this->bookingSeats->pluck('seat_code')->join(', ');
    }
}
