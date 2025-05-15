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
        Schema::create('wallet_transactions', function (Blueprint $table) {
           $table->id();
    $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->string('type'); // 'deposit', 'withdrawal', 'transfer'
    $table->string('description')->nullable();
    $table->string('reference')->nullable();
    $table->timestamps();

    $table->index('wallet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
