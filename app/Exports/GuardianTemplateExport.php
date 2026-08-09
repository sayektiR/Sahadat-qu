<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class GuardianTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            [
                'nama',
                'email',
                'no_telepon',
                'hubungan',
                'alamat'
            ]
        ];
    }
}