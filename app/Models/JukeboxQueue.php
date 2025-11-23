<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class JukeboxQueue extends Model
{
    protected $table = 'jukebox_queue';
    protected $fillable = [
        'user_id',
        'track_name',
        'track_uri'
    ];
}
