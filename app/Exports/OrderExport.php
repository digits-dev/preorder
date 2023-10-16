<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;

class OrderExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function headings():array{
        return [
            'Order Date',
            'Reference #',
            'Channel',
            'Store',
            'Customer Name',
            'Customer Email',
            'Customer Contact',
            'Digits Code',
            'Item Description',
            'Qty',
            'Amount',
            'Payment Method',
            'Payment Status',
            'Invoice #',
            'Claim Status',
            'Claimed Date',
        ];
    }

    public function map($order): array {
        return [
            $order->order_date,
            $order->reference,
            $order->channel_name,
            $order->store_name,
            $order->customer_name,
            $order->email_address,
            $order->contact_number,
            $order->digits_code,
            $order->item_description,
            $order->qty,
            $order->amount,
            $order->payment_method,
            $order->payment_status,
            $order->invoice_number,
            $order->claim_status,
            $order->claimed_date,
        ];
    }

    public function query()
    {
        $orders = Order::query()
            ->leftJoin('campaigns','orders.campaigns_id','=','campaigns.id')
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
                'payment_statuses.status_name as payment_status',
                'payment_methods.payment_method');

        $orders->whereNull('orders.deleted_at');

        if(!CRUDBooster::isSuperAdmin() && !in_array(CRUDBooster::myPrivilegeName(),["Ops","Brands","Accounting"])){
            $orders->where('orders.stores_id',CRUDBooster::myStore());
        }
        if (request()->has('filter_column')) {
            $filter_column = request()->filter_column;

            $orders->where(function($w) use ($filter_column) {
                foreach($filter_column as $key=>$fc) {

                    $value = @$fc['value'];
                    $type  = @$fc['type'];

                    if($type == 'empty') {
                        $w->whereNull($key)->orWhere($key,'');
                        continue;
                    }

                    if($value=='' || $type=='') continue;

                    if($type == 'between') continue;

                    switch($type) {
                        default:
                            if($key && $type && $value) $w->where($key,$type,$value);
                        break;
                        case 'like':
                        case 'not like':
                            $value = '%'.$value.'%';
                            if($key && $type && $value) $w->where($key,$type,$value);
                        break;
                        case 'in':
                        case 'not in':
                            if($value) {
                                $value = explode(',',$value);
                                if($key && $value) $w->whereIn($key,$value);
                            }
                        break;
                    }
                }
            });

            foreach($filter_column as $key=>$fc) {
                $value = @$fc['value'];
                $type  = @$fc['type'];
                $sorting = @$fc['sorting'];

                if($sorting!='') {
                    if($key) {
                        $orders->orderby($key,$sorting);
                        $filter_is_orderby = true;
                    }
                }

                if ($type=='between') {
                    if($key && $value) $orders->whereBetween($key,$value);
                }

                else {
                    continue;
                }
            }
        }
        return $orders;
    }
}
