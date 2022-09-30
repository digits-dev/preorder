<?php

namespace App\Models;

use CRUDBooster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_date',
        'reference',
        'campaigns_id',
        'invoice_number',
        'claiming_invoice_number',
        'claimed_date',
        'channels_id',
        'customers_id',
        'stores_id',
        'total_qty',
        'total_amount',
        'order_statuses_id',
        'payment_methods_id'
    ];

    public function scopeWithDetails($query, $id)
    {
        return $query->where('orders.id',$id)
        ->leftJoin('channels','orders.channels_id','=','channels.id')
        ->leftJoin('stores','orders.stores_id','=','stores.id')
        ->leftJoin('customers','orders.customers_id','=','customers.id')
        ->leftJoin('payment_methods','orders.payment_methods_id','=','payment_methods.id')
        ->leftJoin('payment_statuses','orders.payment_statuses_id','=','payment_statuses.id')
        ->leftJoin('claim_statuses','orders.claim_statuses_id','=','claim_statuses.id')
        ->leftJoin('order_statuses','orders.order_statuses_id','=','order_statuses.id')
        ->select(
            'orders.*',
            'channels.channel_name',
            'stores.store_name',
            'customers.customer_name',
            'customers.email_address',
            'customers.contact_number',
            'order_statuses.status_style as order_status',
            'claim_statuses.status_style as claim_status',
            'payment_statuses.status_style as payment_status',
            'payment_statuses.status_name as pay_status',
            'payment_methods.payment_method')->first();
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $ref = Order::orderBy('created_at','DESC')->max('id')+1;
            $model->created_by = CRUDBooster::myId();
            $model->reference = str_pad($ref, 8, "0", STR_PAD_LEFT);
        });

        static::updating(function($model) {
            $model->updated_by = CRUDBooster::myId();
        });
   }
}
