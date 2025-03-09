<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pi_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pi_payment_id');
            $table->string('txid');
            $table->boolean('verified')->default(false);
            $table->text('_link')->nullable();

            $table->foreign('pi_payment_id')
                  ->references('id')
                  ->on('pi_payments')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pi_transactions');
    }
};
