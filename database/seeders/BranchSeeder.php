<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Kantor Pusat Jakarta',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'latitude' => -6.208763,
                'longitude' => 106.845599,
                'radius' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Bandung',
                'address' => 'Jl. Asia Afrika No. 45, Bandung',
                'latitude' => -6.921553,
                'longitude' => 107.608238,
                'radius' => 150,
                'is_active' => true,
            ],
            [
                'name' => 'Cabang Surabaya',
                'address' => 'Jl. Tunjungan No. 67, Surabaya',
                'latitude' => -7.257472,
                'longitude' => 112.752090,
                'radius' => 120,
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
