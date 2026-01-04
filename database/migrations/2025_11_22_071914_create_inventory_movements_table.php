<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();

            // IN, OUT, WASTE, ADJUSTMENT
            $table->enum('type', ['IN', 'OUT', 'WASTE', 'ADJUSTMENT']);

            // allow decimal KG/Liters/Grams
            $table->decimal('quantity', 12, 3);

            $table->string('cause')->nullable();       // "Order #123" / "Restock"
            $table->foreignId('reference_id')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
