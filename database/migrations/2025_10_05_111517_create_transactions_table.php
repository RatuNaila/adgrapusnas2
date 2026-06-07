<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->enum('tipe', ['pinjam', 'beli']);
            $table->integer('quantity')->default(1);
            $table->enum('status', ['pending', 'active', 'completed', 'returned'])->default('pending');
            $table->timestamp('tanggaltransaksi')->useCurrent();
            $table->timestamp('tanggalkembali')->nullable();
            $table->decimal('totalharga', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
