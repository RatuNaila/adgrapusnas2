<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->nullOnDelete();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('order_code', 32)->unique(); // ex: ORD-20251014-ABC123
            $table->unsignedBigInteger('total')->default(0);      // total (rupiah)
            $table->string('status', 30)->default('pending');      // pending|paid|cancelled|failed (sesuaikan)
            $table->string('payment_status', 30)->default('unpaid');  // unpaid|paid|failed|refunded
            $table->string('idempotency_key', 191)->nullable()->unique(); // opsional
            $table->text('note')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
