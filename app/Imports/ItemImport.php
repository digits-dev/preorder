<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Color;
use App\Models\FreebiesCategory;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemModel;
use App\Models\Size;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;

class ItemImport implements ToModel, WithHeadingRow, WithChunkReading, WithEvents
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Item::updateOrCreate(['digits_code' => $row["digits_code"]],
        [
            'digits_code'       => $row["digits_code"],
            'upc_code'          => $row["upc_code"],
            'item_description'  => $row["item_description"],
            'brands_id'         => Brand::withName($row["brand"])->id,
            // 'item_categories_id' => ItemCategory::withName($row["category"])->id,
            'colors_id'         => Color::withName($row["actual_color"])->id,
            'item_models_id'    => ItemModel::withName($row["model"])->id,
            'sizes_id'          => Size::withName($row["size"])->id,
            'current_srp'       => $row["current_srp"],
            'dtc_wh'            => $row["wh_qty"],
            'dtc_reserved_qty'  => $row["wh_qty"],
            'included_freebies' => (empty($row["included_freebie"])) ? null : rtrim(FreebiesCategory::withFreebie($row["included_freebie"]),","),
            'is_freebies'       => (($row["item_type"]) == "MAIN ITEM") ? 0 : 1,
            'freebies_categories_id' => (empty($row["freebie_category"])) ? null : FreebiesCategory::withName($row["freebie_category"])->id,
            'campaigns_id'      => (empty($row["campaign"])) ? null : Campaign::withName($row["campaign"])->id
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                Log::info('Import item started at: ' . now());
            },
            AfterImport::class => function(AfterImport $event) {
                Log::info('Import item completed at: ' . now());
                CRUDBooster::insertLog(cbLang("log_add", ['name' => "Import item completed!", 'module' => "Items"]));
            },
            ImportFailed::class => function(ImportFailed $event) {
                Log::error('Import item failed with error: ' . $event->getException()->getMessage());
            },
        ];
    }
}
