<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@absensi.com',
            'password' => Hash::make('password'),
            'role_id' => 1,
            'is_active' => true,
        ]);

        $adminCabangJakarta = User::create([
            'name' => 'Admin Jakarta',
            'email' => 'admin.jakarta@absensi.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'branch_id' => 1,
            'is_active' => true,
        ]);

        $adminCabangBandung = User::create([
            'name' => 'Admin Bandung',
            'email' => 'admin.bandung@absensi.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'branch_id' => 2,
            'is_active' => true,
        ]);

        $karyawan1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@absensi.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'branch_id' => 1,
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $karyawan1->id,
            'nik' => 'EMP001',
            'full_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'branch_id' => 1,
            'position_id' => 3,
            'join_date' => now()->subYear(),
            'is_active' => true,
        ]);

        $karyawan2 = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@absensi.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
            'branch_id' => 1,
            'is_active' => true,
        ]);

        Employee::create([
            'user_id' => $karyawan2->id,
            'nik' => 'EMP002',
            'full_name' => 'Siti Rahayu',
            'phone' => '081234567891',
            'branch_id' => 1,
            'position_id' => 3,
            'join_date' => now()->subMonths(6),
            'is_active' => true,
        ]);
    }
}
