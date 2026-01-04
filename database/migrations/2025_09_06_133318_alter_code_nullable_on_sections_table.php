<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Make code nullable
            $table->string('code', 50)->nullable()->change();
            // (Optional) ensure slug is nullable if you want that too:
            // $table->string('slug', 160)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->string('code', 50)->nullable(false)->change();
            // $table->string('slug', 160)->nullable(false)->change();
        });
    }
};
