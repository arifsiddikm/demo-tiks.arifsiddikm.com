<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class News extends Model {
    protected $table = 'news';
    protected $fillable = [
        'title','slug','excerpt','content','thumbnail',
        'author_id','category','is_published','published_at'
    ];
    protected $casts = ['published_at' => 'datetime', 'is_published' => 'boolean'];

    public function author() { return $this->belongsTo(User::class, 'author_id'); }

    public function getThumbnailUrlAttribute() {
        if ($this->thumbnail) {
            // Support external URLs (Unsplash, etc.)
            if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                return $this->thumbnail;
            }
            if (Storage::disk('public')->exists($this->thumbnail)) {
                return asset('storage/' . $this->thumbnail);
            }
        }
        return asset('images/default-news.jpg');
    }
}
