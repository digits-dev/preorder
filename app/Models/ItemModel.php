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
        'is_freebies',
        'model_name',
        'status',
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
