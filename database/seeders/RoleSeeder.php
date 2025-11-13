<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Super Administrator dengan akses penuh ke seluruh sistem',
            ],
            [
                'name' => 'admin_cabang',
                'display_name' => 'Admin Cabang',
                'description' => 'Administrator cabang dengan akses terbatas pada satu cabang',
            ],
            [
                'name' => 'karyawan',
                'display_name' => 'Karyawan',
                'description' => 'Karyawan yang dapat melakukan absensi dan pengajuan cuti/izin',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
