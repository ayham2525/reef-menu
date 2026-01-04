<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_category_id')->nullable()
                ->constrained('menu_categories')->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable();

            $table->text('description')->nullable();

            $table->decimal('price', 10, 2)->default(0);
            $table->char('currency', 3)->default('AED');

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('prep_time_minutes')->nullable();
            $table->unsignedInteger('calories')->nullable();

            $table->json('tags')->nullable();       // e.g. ["vegan","spicy"]
            $table->json('allergens')->nullable();  // e.g. ["nuts","gluten"]

            $table->unsignedInteger('sort_order')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['menu_category_id', 'is_active', 'is_available']);
            $table->index(['is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
