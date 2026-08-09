<?php

namespace App\Imports;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuardianImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $branchId = Auth::user()->branch_id;

        return DB::transaction(function () use ($row, $branchId) {

            $user = User::create([
                'branch_id' => $branchId,
                'name' => trim($row['nama']),
                'email' => trim($row['email']),
                'password' => Hash::make('password123'),
                'role' => 'guardian',
                'phone' => $row['no_telepon'] ?? null,
                'address' => $row['alamat'] ?? null,
                'is_active' => true,
            ]);

            return Guardian::create([
                'user_id' => $user->id,
                'name' => trim($row['nama']),
                'phone' => $row['no_telepon'] ?? null,
                'address' => $row['alamat'] ?? null,
                'relation' => $row['hubungan'] ?? null,
            ]);
        });
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'no_telepon' => [
                'nullable',
                'max:15',
            ],

            'hubungan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],
        ];
    }
}