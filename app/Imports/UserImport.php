<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Store;
use App\Models\Channel;
use App\Models\Privilege;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UserImport implements ToModel, WithHeadingRow, WithChunkReading
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        User::firstOrCreate([
            'name'          => strtoupper($row["name"]),
            'email'         => $row["email"],
            'password'      => bcrypt("qwerty2022"),
            'stores_id'     => (empty($row["store"])) ? null : Store::withName($row["store"])->id,
            'channels_id'   => (empty($row["channel"])) ? null : Channel::withName($row["channel"])->id,
            'id_cms_privileges'      => Privilege::withName($row["privilege"])->id,
            'status'        => "ACTIVE"
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
