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
        'payment_statuses_id',
        'payment_methods_id'
    ];

    public function scopeWithCustomerOrder($query, $customer, $campaign){
        return $query->where('customers_id',$customer)
            ->where('campaigns_id',$campaign)
            ->where('payment_statuses_id','!=',2)
            ->select('id')->get()->count();
    }

    public function scopeWithDetails($query, $id){
        return $query->where('orders.id',$id)
        ->leftJoin('channels','orders.channels_id','=','channels.id')
        ->leftJoin('stores','orders.stores_id','=','stores.id')
        ->leftJoin('concepts','stores.concepts_id','=','concepts.id')
        ->leftJoin('customers','orders.customers_id','=','customers.id')
        ->leftJoin('payment_methods','orders.payment_methods_id','=','payment_methods.id')
        ->leftJoin('payment_statuses','orders.payment_statuses_id','=','payment_statuses.id')
        ->leftJoin('claim_statuses','orders.claim_statuses_id','=','claim_statuses.id')
        ->leftJoin('order_statuses','orders.order_statuses_id','=','order_statuses.id')
        ->select(
            'orders.*',
            'channels.channel_name',
            'stores.store_name',
            'concepts.concept_logo',
            'customers.customer_name',
            'customers.email_address',
            'customers.contact_number',
            'order_statuses.status_style as order_status',
            'claim_statuses.status_style as claim_status',
            'payment_statuses.status_style as payment_status',
            'payment_statuses.status_name as pay_status',
            'payment_methods.payment_method')->first();
    }

    public function scopeGetExport($query){
        return $query->leftJoin('campaigns','orders.campaigns_id','=','campaigns.id')
            ->leftJoin('channels','orders.channels_id','=','channels.id')
            ->leftJoin('stores','orders.stores_id','=','stores.id')
            ->leftJoin('customers','orders.customers_id','=','customers.id')
            ->leftJoin('payment_methods','orders.payment_methods_id','=','payment_methods.id')
            ->leftJoin('payment_statuses','orders.payment_statuses_id','=','payment_statuses.id')
            ->leftJoin('claim_statuses','orders.claim_statuses_id','=','claim_statuses.id')
            ->join('order_lines','orders.id','=','order_lines.orders_id')
            ->join('items','order_lines.digits_code','=','items.digits_code')
            ->select(
                'orders.*',
                'channels.channel_name',
                'stores.store_name',
                'customers.customer_name',
                'customers.email_address',
                'customers.contact_number',
                'order_lines.digits_code',
                'items.item_description',
                'order_lines.amount',
                'order_lines.qty',
                'claim_statuses.status_name as claim_status',
                'order_lines.claiming_invoice_number as claim_invoice_number',
                'order_lines.claimed_date as claim_date',
                'payment_statuses.status_name as payment_status',
                'payment_methods.payment_method')->whereNull('orders.deleted_at');
    }

    public static function boot(){
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
