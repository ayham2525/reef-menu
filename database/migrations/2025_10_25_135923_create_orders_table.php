<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();                 // consistent with uuids in app
            $table->string('code')->unique();              // human-friendly order code (e.g., ORD-XYZ123)

            // Who placed the order (exactly one of these should be set)
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('broker_id')->nullable();
            $table->foreign('broker_id')->references('id')->on('brokers')->nullOnDelete();

            $table->boolean('is_free')->default(false);    // brokers=true, employees=false
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending|paid|cancelled|fulfilled etc.
            $table->timestamp('placed_at')->nullable();    // when submitted/confirmed
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Fast lookups for the "max 3/day for broker" rule
            $table->index(['broker_id', 'placed_at']);
            $table->index(['employee_id', 'placed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
