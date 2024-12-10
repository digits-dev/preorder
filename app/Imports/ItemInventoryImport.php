<?php

namespace App\Imports;

use App\Models\Item;
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

class ItemInventoryImport implements ToModel, WithHeadingRow, WithChunkReading, WithEvents
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $digitsCode = (string) $row["digits_code"];
        Item::where('digits_code', $digitsCode)->update([
            'dtc_wh' => $row["qty"],
            'dtc_reserved_qty' => $row["qty"]
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
                Log::info('Import item inventory started at: ' . now());
            },
            AfterImport::class => function(AfterImport $event) {
                Log::info('Import item inventory completed at: ' . now());
                CRUDBooster::insertLog("Import item completed!");
            },
            ImportFailed::class => function(ImportFailed $event) {
                Log::error('Import item inventory failed with error: ' . $event->getException()->getMessage());
            },
        ];
    }
}
