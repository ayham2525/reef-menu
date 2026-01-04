<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('inventory_item_recipes', function (Blueprint $table) {
            $table->id();

            // menu item (the product sold)
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();

            // ingredient (raw material)
            $table->foreignId('ingredient_item_id')->constrained('menu_items')->cascadeOnDelete();

            // how much ingredient is used in 1 unit
            $table->decimal('quantity', 12, 3);

            $table->string('unit_type'); // kg, g, ml, unit

            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_item_recipes');
    }
};
