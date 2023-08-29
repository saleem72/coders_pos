<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Invoice::factory()
            ->count(30)
            ->hasInvoiceItems(5)
            ->create();

        Invoice::factory()
            ->count(60)
            ->hasInvoiceItems(8)
            ->create();

        Invoice::factory()
            ->count(10)
            ->hasInvoiceItems(12)
            ->create();
    }
}
