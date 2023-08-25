<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class Size extends Model
{
    use HasFactory;

    protected $table = 'sizes';

    protected $fillable = [
        'size',
        'status',
        'created_by',
        'updated_by'
    ];

    public function scopeWithName($query, $size)
    {
        return $query->where('size',$size)->where('status','ACTIVE')->first();
    }

    public static function boot()
    {
       parent::boot();
       static::creating(function($model)
       {
           $model->created_by = CRUDBooster::myId();
       });
       static::updating(function($model)
       {
           $model->updated_by = CRUDBooster::myId();
       });
   }
}
