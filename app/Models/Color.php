<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colors';

    protected $fillable = [
        'color_name',
        'status',
        'created_by',
        'updated_by'
    ];

    public function scopeWithName($query, $color)
    {
        return $query->where('color_name',$color)->where('status','ACTIVE')->first();
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
