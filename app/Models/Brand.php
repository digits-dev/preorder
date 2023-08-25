<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'brand_code',
        'brand_name',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function scopeWithName($query, $brand)
    {
        return $query->where('brand_name',$brand)->where('status','ACTIVE')->first();
    }
}
