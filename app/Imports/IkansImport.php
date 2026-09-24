<?php

namespace App\Imports;

use App\Ikan;
use Maatwebsite\Excel\Concerns\ToModel;

class IkansImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Ikan([
            //
        ]);
    }
}
