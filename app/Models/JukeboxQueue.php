<?php

use App\Models\User;

class JukeboxQueue extends Model
{
    protected $table = 'jukebox_queue';

    protected $fillable = [
        'user_id',
        'track_name',
        'track_uri',
        'is_playing'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}