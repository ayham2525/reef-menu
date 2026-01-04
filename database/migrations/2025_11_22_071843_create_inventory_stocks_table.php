<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();

            /* ================================
             *  PROFESSIONAL STOCK MANAGEMENT
             * ================================ */

            // unit type (kg, g, liter, ml, unit, packs, carton, etc.)
            $table->string('unit_type')->default('unit');

            // conversion factor to base unit
            // Example: 1kg = 1000g → multiplier = 1000
            $table->decimal('unit_multiplier', 10, 2)->default(1);

            // current stock level
            $table->decimal('quantity', 12, 3)->default(0);

            // minimum allowed before warning
            $table->decimal('min_quantity', 12, 3)->default(0);

            // true = alert raised once (prevents spam)
            $table->boolean('is_low_stock')->default(false);

            // last time a low-stock notification was sent
            $table->timestamp('low_stock_notified_at')->nullable();

            $table->timestamp('last_restocked_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
