<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class StudentTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            [
                'nik',
                'nama',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'alamat',
                'kelompok',
                'wali_santri',
                'status'
            ]
        ];
    }
}