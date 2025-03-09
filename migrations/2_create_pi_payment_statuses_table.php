<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pi_payment_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pi_payment_id');
            $table->boolean('developer_approved')->default(false);
            $table->boolean('transaction_verified')->default(false);
            $table->boolean('developer_completed')->default(false);
            $table->boolean('canceled')->default(false);
            $table->boolean('user_cancelled')->default(false);

            $table->foreign('pi_payment_id')
                  ->references('id')
                  ->on('pi_payments')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pi_payment_statuses');
    }
};
