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
        Schema::create('warehouse_transfer_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transfer_id')->constrained('warehouse_transfers')->cascadeOnDelete();

            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();

            $table->decimal('quantity', 12, 3);
            $table->string('unit_type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_transfer_items');
    }
};
