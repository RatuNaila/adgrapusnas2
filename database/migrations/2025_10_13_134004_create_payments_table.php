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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');              // nominal yang harus dibayar (rupiah)
            $table->string('method', 30)->nullable();          // transfer|qris|cc|mock
            $table->string('status', 30)->default('pending');  // pending|paid|failed|cancelled
            $table->string('reference', 100)->nullable();      // nomor referensi gateway/mock
            $table->timestamp('paid_at')->nullable();
            $table->text('raw_payload')->nullable();           // simpan respons/mock data
            $table->unique(['order_id']); // 1 order : 1 payment (sederhana)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
