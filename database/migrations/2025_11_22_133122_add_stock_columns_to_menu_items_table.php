<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->decimal('stock_quantity', 10, 3)->default(0)->after('price');
            $table->decimal('min_stock_alert', 10, 3)->default(0)->after('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
            $table->dropColumn('min_stock_alert');
        });
    }
};
