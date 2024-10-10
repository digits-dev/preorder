<?php

namespace App\Exports;

use App\Http\Helpers\Helper;
use App\Models\Order;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

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
            'Claiming Invoice #'
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
            (empty($order->claimed_date)) ? $order->claim_date : $order->claimed_date,
            (empty($order->claiming_invoice_number)) ? $order->claim_invoice_number : $order->claiming_invoice_number,
        ];
    }

    public function query()
    {
        $orders = Order::query()->getExport();

        if(!CRUDBooster::isSuperAdmin() && !in_array(CRUDBooster::myPrivilegeName(),["Ops","Brands","Accounting"])){
            $orders->where('orders.stores_id', Helper::myStore());
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
