<?php

namespace App\Imports;

use App\Models\Store;
use App\Models\Channel;
use App\Models\Concept;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StoreImport implements ToModel, WithHeadingRow, WithChunkReading
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Store::firstOrCreate([
            'store_name'    => $row["store_name"],
            'channels_id'   => Channel::withName($row["channel"])->id,
            'concepts_id'   => Concept::withName($row["concept"])->id,
            'status'        => $row["status"]
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
