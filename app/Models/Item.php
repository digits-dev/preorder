<?php

namespace App\Models;

use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $connection = 'mysql';

    protected $fillable = [
        'digits_code',
        'upc_code',
        'item_description',
        'brands_id',
        // 'item_categories_id',
        'item_models_id',
        'sizes_id',
        'colors_id',
        'actual_color',
        'current_srp',
        'dtc_wh',
        'dtc_reserved_qty',
        'tier',
        'included_freebies',
        'is_freebies',
        'freebies_categories_id',
        'campaigns_id'
    ];

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
