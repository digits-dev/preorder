<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ItemInventoryImport implements ToModel, WithHeadingRow, WithChunkReading
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Item::where('digits_code',$row["digits_code"])->update([
            'dtc_wh' => $row["qty"],
            'dtc_reserved_qty' => $row["qty"]
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
