<?php

namespace App\Exports;

use App\Models\FreebiesCategory;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;

class ItemExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function headings():array{
        return [
            'Digits Code',
            'UPC Code',
            'Item Description',
            'Brand',
            'Model',
            'Size',
            'Actual Color',
            'Current SRP',
            'Campaign',
            'Included Freebie',
            'Is Freebie',
            'Freebie Category',
            'Available Qty'
        ];
    } 

    public function map($item): array {
        
        return [
            $item->digits_code,
            $item->upc_code,
            $item->item_description,
            $item->brand_name,
            $item->model_name,
            $item->size,
            $item->color_name,
            $item->current_srp,
            $item->campaigns_name,
            rtrim(FreebiesCategory::withCategory($item->included_freebies),","),
            ($item->is_freebies == 0) ? 'No' : 'Yes',
            $item->category_name,
            $item->dtc_reserved_qty,
        ];
    }

    public function query()
    {
        $items = Item::query()
            ->leftJoin('brands','items.brands_id','=','brands.id')
            ->leftJoin('colors','items.colors_id','=','colors.id')
            ->leftJoin('sizes','items.sizes_id','=','sizes.id')
            ->leftJoin('campaigns','items.campaigns_id','=','campaigns.id')
            ->leftJoin('item_models','items.item_models_id','=','item_models.id')
            ->leftJoin('freebies_categories','items.freebies_categories_id','=','freebies_categories.id')
            ->select(
                'items.*',
                'brands.brand_name',
                'colors.color_name',
                'item_models.model_name',
                'sizes.size',
                'freebies_categories.category_name',
                'campaigns.campaigns_name');

        if (request()->has('filter_column')) {
            $filter_column = request()->filter_column;

            $items->where(function($w) use ($filter_column) {
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
                        $items->orderby($key,$sorting);
                        $filter_is_orderby = true;
                    }
                }

                if ($type=='between') {
                    if($key && $value) $items->whereBetween($key,$value);
                }

                else {
                    continue;
                }
            }
        }
        return $items;
    }
}
