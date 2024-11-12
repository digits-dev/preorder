<?php

namespace App\Imports;

use App\Models\Store;
use App\Models\Channel;
use App\Models\Concept;
use Illuminate\Support\Facades\Cache;
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
        $cacheChannelKey = 'channel_'.trim($row["channel"]);
        $cacheConceptKey = 'concept_'.trim($row["concept"]);

        $channel = Cache::remember($cacheChannelKey, 60, function () use ($row) {
            return Channel::withName($row["channel"])->id;
        });

        $concept = Cache::remember($cacheConceptKey, 60, function () use ($row) {
            return Concept::withName($row["concept"])->id;
        });

        Store::firstOrCreate([
            'store_name'    => $row["store_name"],
            'channels_id'   => $channel ?? null,
            'concepts_id'   => $concept ?? null,
            'status'        => $row["status"]
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
