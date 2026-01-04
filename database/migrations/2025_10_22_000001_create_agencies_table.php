<?php

// database/migrations/2025_10_22_000001_create_agencies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('name');
            $t->string('code')->unique();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('license_no')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
