<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $table = 'channels';

    public function scopeWithName($query, $channel)
    {
        return $query->where('channel_name',$channel)->where('status','ACTIVE')->first();
    }
}
