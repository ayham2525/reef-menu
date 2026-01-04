<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'menu_item_id')) {
                $table->unsignedBigInteger('menu_item_id')->nullable()->after('order_id');
                $table->foreign('menu_item_id')->references('id')->on('menu_items')->nullOnDelete();
            }

            if (!Schema::hasColumn('order_items', 'option_id')) {
                $table->unsignedBigInteger('option_id')->nullable()->after('menu_item_id');
                $table->foreign('option_id')->references('id')->on('menu_item_option_values')->nullOnDelete();
            }

            if (!Schema::hasColumn('order_items', 'option_name')) {
                $table->string('option_name')->nullable()->after('item_name');
            }
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('menu_item_id');
            $table->dropConstrainedForeignId('option_id');
            $table->dropColumn('option_name');
        });
    }
};
