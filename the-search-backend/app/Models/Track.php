<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'spotify_id',
        'name',
        'number',
        'album_id',
        'status',
        'seconds'

    ];

    public function album() {
        return $this->belongsTo(Album::class, 'album_id');
    }

    public function artists() {
        return $this->belongsToMany(Artist::class, 'artist_id');
    }
}
