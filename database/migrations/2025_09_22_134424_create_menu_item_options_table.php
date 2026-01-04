<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_item_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_item_id')
                ->constrained('menu_items')->cascadeOnDelete();

            $table->string('name'); // e.g. Size, Sauce, Extras
            $table->enum('type', ['single', 'multiple'])->default('single'); // radio vs checkbox
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('min_choices')->nullable(); // for multiple
            $table->unsignedInteger('max_choices')->nullable(); // for multiple

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['menu_item_id', 'is_active']);
            $table->index(['sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_options');
    }
};
