<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    //
    protected $fillable = [
        'spotify_id',
        'name'
    ];
    public function albums() {
        return $this->belongsToMany(Album::class);
    }

    public function tracks() {
        return $this->belongsToMany(Track::class);
    }
}
