<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model {
    protected $fillable = ['schedule_id','row','number','code','status'];
    public function schedule() { return $this->belongsTo(FilmSchedule::class, 'schedule_id'); }
}
