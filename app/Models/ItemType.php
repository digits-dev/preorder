<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class ItemType extends Model
{
    use HasFactory;

    protected $table = 'item_types';

    protected $fillable = [
        'item_type',
        'status',
        'created_by',
        'updated_by'
    ];

    public function scopeWithName($query, $model)
    {
        return $query->where('item_type',$model)->where('status','ACTIVE')->first();
    }

    public static function boot()
    {
       parent::boot();
       static::creating(function($model)
       {
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = CRUDBooster::myId();
       });
       static::updating(function($model)
       {
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updated_by = CRUDBooster::myId();
       });
   }
}
