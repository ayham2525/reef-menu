<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // 🔹 Drop existing unique index on slug
            $table->dropUnique('menu_items_slug_unique'); // adjust name if different

            // 🔹 Add new composite unique index including deleted_at
            //    This allows same slug to exist again after soft delete.
            $table->unique(['slug', 'deleted_at'], 'menu_items_slug_deleted_at_unique');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Revert changes — remove the composite unique index
            $table->dropUnique('menu_items_slug_deleted_at_unique');

            // Restore original unique constraint on slug
            $table->unique('slug', 'menu_items_slug_unique');
        });
    }
};
