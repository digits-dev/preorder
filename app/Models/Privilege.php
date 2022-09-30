<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    use HasFactory;

    protected $table = 'cms_privileges';

    public function scopeWithName($query, $privilege)
    {
        return $query->where('name',$privilege)->first();
    }
}
