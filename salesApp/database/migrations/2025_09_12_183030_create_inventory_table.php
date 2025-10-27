<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 20)->unique();
            $table->string('product_name', 150);
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->integer('stock_quantity');
            $table->string('category', 100);
            $table->string('supplier', 150)->nullable();
            $table->date('last_restocked')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};