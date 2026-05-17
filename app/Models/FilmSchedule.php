<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FilmSchedule extends Model {
    protected $table = 'film_schedules';
    protected $fillable = [
        'film_id','cinema_id','show_date','show_time','studio',
        'film_type','total_seats','available_seats','price','is_active'
    ];
    protected $casts = ['show_date' => 'date', 'price' => 'decimal:2'];

    public function film()     { return $this->belongsTo(Film::class); }
    public function cinema()   { return $this->belongsTo(Cinema::class); }
    public function seats()    { return $this->hasMany(Seat::class, 'schedule_id'); }
    public function bookings() { return $this->hasMany(Booking::class, 'schedule_id'); }

    public function getFormattedPriceAttribute() {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
    public function getFormattedTimeAttribute() {
        return \Carbon\Carbon::createFromFormat('H:i:s', $this->show_time)->format('H:i');
    }
}
