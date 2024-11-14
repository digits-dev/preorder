<?php

namespace App\Models;

use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaigns';

    protected $fillable = [
        'campaigns_name',
        'max_order_count',
        'status',
    ];

    public function scopeWithName($query, $campaign)
    {
        return $query->where('campaigns_name',$campaign)
            ->where('status','ACTIVE')->first();
    }

    public function scopeWithOrderLimit($query, $id)
    {
        return $query->where('id',$id)->where('status','ACTIVE')
            ->value('max_order_count');
    }

    public function scopeActive($query)
    {
        return $query->where('status','ACTIVE');
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
