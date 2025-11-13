<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Manager', 'description' => 'Manajer cabang', 'is_active' => true],
            ['name' => 'Supervisor', 'description' => 'Supervisor tim', 'is_active' => true],
            ['name' => 'Staff', 'description' => 'Staff operasional', 'is_active' => true],
            ['name' => 'Operator', 'description' => 'Operator shift', 'is_active' => true],
            ['name' => 'Admin', 'description' => 'Staff administrasi', 'is_active' => true],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
