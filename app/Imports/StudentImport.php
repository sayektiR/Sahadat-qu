<?php

namespace App\Imports;

use App\Models\Group;
use App\Models\Guardian;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StudentImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $branchId = Auth::user()->branch_id;

        /*
         * Cari kelompok berdasarkan nama
         * dan pastikan kelompok berada di branch admin.
         */
        $group = Group::where('branch_id', $branchId)
            ->where('name', trim($row['kelompok']))
            ->first();

        if (! $group) {
            throw new \RuntimeException(
                'Kelompok "' . ($row['kelompok'] ?? '-') . '" tidak ditemukan.'
            );
        }

        /*
         * Cari wali berdasarkan nama
         * dan pastikan wali berada di branch admin.
         */
        $guardian = null;

        if (! empty($row['wali_santri'])) {
            $guardian = Guardian::where('name', trim($row['wali_santri']))
                ->whereHas('user', function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->first();

            if (! $guardian) {
                throw new \RuntimeException(
                    'Wali santri "' . $row['wali_santri'] . '" tidak ditemukan.'
                );
            }
        }

        /*
         * Buat NIS otomatis menggunakan pola
         * yang sama dengan tambah santri manual.
         */
        $birthDateValue = $row['tanggal_lahir'];
        if (is_numeric($birthDateValue)) {
            $birthDate = Carbon::instance(
                ExcelDate::excelToDateTimeObject($birthDateValue)
            );
        } else {
            $birthDate = Carbon::parse($birthDateValue);
        }

        $nis = 'SQ-' .
            $birthDate->format('dm') .
            substr((string) $row['nik'], -3);

        /*
         * Simpan data dalam transaction.
         */
        return DB::transaction(function () use (
            $branchId,
            $group,
            $guardian,
            $row,
            $birthDate,
            $nis
        ) {

            return new Student([
                'branch_id' => $branchId,
                'group_id' => $group->id,
                'guardian_id' => $guardian?->id,
                'nis' => $nis,
                'nik' => (string) $row['nik'],
                'name' => trim($row['nama']),
                'birth_place' => $row['tempat_lahir'] ?? null,
                'birth_date' => $birthDate->format('Y-m-d'),
                'gender' => strtolower(trim($row['jenis_kelamin'])),
                'address' => $row['alamat'] ?? null,
                'photo' => null,
                'status' => strtolower(trim($row['status'] ?? 'active')),
            ]);
        });
    }

    public function rules(): array
    {
        return [
            'nik' => [
                'required',
                'digits:16',
                'unique:students,nik',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'tempat_lahir' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tanggal_lahir' => [
                'required',
            ],

            'jenis_kelamin' => [
                'required',
                'in:male,female',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

            'kelompok' => [
                'required',
                'string',
            ],

            'wali_santri' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ];
    }
}