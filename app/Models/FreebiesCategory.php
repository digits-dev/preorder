<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class FreebiesCategory extends Model
{
    use HasFactory;

    protected $table = 'freebies_categories';

    protected $fillable = [
        'category_name',
        'status',
    ];

    public function scopeWithName($query, $category)
    {
        return $query->where('category_name',$category)->where('status','ACTIVE')->first();
    }

    public function scopeWithFreebie($query, $category)
    {
        $categories = explode(",",$category);
        $freebies = $query->whereIn('category_name',$categories)
            ->where('status','ACTIVE')
            ->select('id')->get();

        $data = '';
        foreach ($freebies as $value) {
            $data .= $value->id.',';
        }
        return $data;
    }

    public function scopeWithCategory($query, $category)
    {
        $categories = explode(",",$category);
        $freebies = $query->whereIn('id',$categories)
            ->where('status','ACTIVE')
            ->select('category_name')->get();

        $data = '';
        foreach ($freebies as $value) {
            $data .= $value->category_name.',';
        }
        return $data;
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
