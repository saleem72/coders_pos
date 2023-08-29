<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()
            ->count(1)
            ->hasProducts(2)
            ->create();

        Category::factory()
            ->count(5)
            ->hasProducts(5)
            ->create();

        Category::factory()
            ->count(1)
            ->hasProducts(3)
            ->create();

        Category::factory()
            ->count(5)
            ->hasProducts(4)
            ->create();
    }
}
