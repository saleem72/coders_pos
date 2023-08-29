<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // $table->integer('category_id')->unsigned();
            $table->foreignId('category_id')->constrained(
                table: 'categories'
            );
            // $table->integer('unit_id')->unsigned();
            $table->foreignId('unit_id')->constrained(
                table: 'units'
            );

            $table->string('name', 255);
            $table->double('purchase', 15, 2);
            $table->double('retail', 15, 2);
            $table->integer('quantity')->unsigned();
            $table->string('barcode', 255);
            $table->string('image', 255)->nullable();
            $table->string('image_extension', 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
