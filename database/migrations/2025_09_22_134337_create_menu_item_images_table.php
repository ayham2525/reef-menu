<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_item_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_item_id')
                ->constrained('menu_items')->cascadeOnDelete();

            $table->string('disk')->nullable();
            $table->string('path');
            $table->string('alt_text')->nullable();

            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['menu_item_id', 'is_primary']);
            $table->index(['sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_images');
    }
};
