<?php

namespace App\Models;

use CRUDBooster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    use HasFactory;

    protected $table = 'order_lines';

    protected $fillable = [
        'orders_id',
        'digits_code',
        'qty',
        'amount',
        'available_qty'
    ];

    public function scopeWithDetails($query, $id)
    {
        return $query->where('order_lines.orders_id',$id)
            ->leftJoin('items','order_lines.digits_code','=','items.digits_code')
            ->select('order_lines.*', 'items.item_description')->get();
    }
}
