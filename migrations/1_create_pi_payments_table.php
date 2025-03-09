<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pi_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('identifier')->unique();
            $table->string('user_uid');
            $table->decimal('amount', 10, 7);
            $table->text('memo');
            $table->json('metadata');
            $table->string('from_address');
            $table->string('to_address');
            $table->string('direction');
            $table->string('network');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pi_payments');
    }
};
