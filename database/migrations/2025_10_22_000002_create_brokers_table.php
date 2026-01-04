<?php
// database/migrations/2025_10_22_000002_create_brokers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brokers', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->uuid('agency_id')->nullable()->index();
            $t->string('name');
            $t->string('email')->nullable()->unique();
            $t->string('phone')->nullable();
            $t->string('brn')->nullable(); // Broker Registration Number
            $t->boolean('is_active')->default(true);
            $t->timestamps();

            $t->foreign('agency_id')->references('id')->on('agencies')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('brokers');
    }
};
