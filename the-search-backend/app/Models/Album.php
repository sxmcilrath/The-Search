<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    //
    protected $fillable = [
        'spotify_id',
        'name',
        'release_date',
        'pure_score', // maybe don't need
        'vibe_score', 
        'status',
        'image_url'
    ];

    public function artists() {
        return $this->belongsToMany(Artist::class);
    }

    public function tracks() {
        return $this->hasMany(Track::class);
    }
}
