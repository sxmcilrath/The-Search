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
        'image_url',
        'user_id'
    ];

    public function artists() {
        return $this->belongsToMany(Artist::class);
    }

    public function tracks() {
        return $this->hasMany(Track::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
