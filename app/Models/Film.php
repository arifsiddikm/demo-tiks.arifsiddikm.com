<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Film extends Model {
    protected $fillable = [
        'title','slug','synopsis','poster','trailer_url','trailer_photo',
        'duration','rating','language','director','cast','release_date',
        'status','is_active'
    ];

    protected $casts = [
        'release_date' => 'date',  // auto-cast ke Carbon
        'is_active'    => 'boolean',
    ];

    public function genres()    { return $this->belongsToMany(Genre::class, 'film_genres'); }
    public function schedules() { return $this->hasMany(FilmSchedule::class); }

    public function getPosterUrlAttribute(): string
    {
        if ($this->poster) {
            // Support external URLs (Unsplash, etc.)
            if (str_starts_with($this->poster, 'http://') || str_starts_with($this->poster, 'https://')) {
                return $this->poster;
            }
            return asset('storage/' . $this->poster);
        }
        return asset('images/default-poster.jpg');
    }

    public function getCastArrayAttribute(): array
    {
        return array_map('trim', explode(',', $this->cast ?? ''));
    }
}
