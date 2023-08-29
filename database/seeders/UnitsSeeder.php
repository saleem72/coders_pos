<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::create([
            'name' => 'Box'
        ]);

        Unit::create([
            'name' => 'Piece'
        ]);

        Unit::create([
            'name' => 'Kilo'
        ]);

        Unit::create([
            'name' => 'Meter'
        ]);
    }
}
