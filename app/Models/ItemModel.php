<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class ItemModel extends Model
{
    use HasFactory;

    protected $table = 'item_models';

    protected $fillable = [
        'model_name',
        'status',
        'created_by',
        'updated_by'
    ];

    public function scopeWithName($query, $model)
    {
        return $query->where('model_name',$model)->where('status','ACTIVE')->first();
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
