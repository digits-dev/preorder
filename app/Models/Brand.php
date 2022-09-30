<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    public function scopeWithName($query, $brand)
    {
        return $query->where('brand_name',$brand)->where('status','ACTIVE')->first();
    }
}
