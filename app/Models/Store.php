<?php

namespace App\Models;

use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    use HasFactory;

    protected $table = 'stores';

    protected $fillable = [
        'concepts_id',
        'channels_id',
        'store_name',
        'status',
        'created_by'
    ];

    public function channel() : BelongsTo {
        return $this->belongsTo(Channel::class, 'channels_id', 'id');
    }

    public function concept() : BelongsTo {
        return $this->belongsTo(Concept::class, 'concepts_id', 'id');
    }

    public function scopeWithName($query, $store)
    {
        return $query->where('store_name',$store)->where('status','ACTIVE')->first();
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
