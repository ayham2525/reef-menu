<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_item_option_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_item_option_id')
                ->constrained('menu_item_options')->cascadeOnDelete();

            $table->string('label');                 // e.g. Small, Medium, Large
            $table->decimal('price_delta', 10, 2)->default(0); // +/− price
            $table->boolean('is_default')->default(false);

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['menu_item_option_id', 'is_active']);
            $table->index(['sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_option_values');
    }
};
