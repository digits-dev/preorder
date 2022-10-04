<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class ItemCategory extends Model
{
    use HasFactory;

    protected $table = 'item_categories';

    protected $fillable = [
        'category_name',
        'status',
    ];

    public function scopeWithName($query, $category)
    {
        return $query->where('category_name',$category)->where('status','ACTIVE')->first();
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
