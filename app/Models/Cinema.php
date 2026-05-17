<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model {
    protected $fillable = ['city_id','name','slug','address','phone','is_active'];
    public function city()      { return $this->belongsTo(City::class); }
    public function schedules() { return $this->hasMany(FilmSchedule::class); }
}
