<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();

            // Core identifiers
            $table->string('name', 150);
            $table->string('slug', 160)->unique();      // URL/code-friendly unique key
            $table->string('code', 50)->unique();       // Optional human-friendly code (e.g., HR, FIN)

            // Optional hierarchy (parent section)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('sections')
                ->nullOnDelete();

            // Meta
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();

            // Auditing (who created/updated)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            // Helpful composite index for lookups
            $table->index(['parent_id', 'sort_order']);
            $table->index(['slug', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
