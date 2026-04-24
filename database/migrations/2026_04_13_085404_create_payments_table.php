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
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_client_secret')->nullable();
            $table->float('amount');
            $table->enum('status', [
                'requires_payment_method',
                'requires_action',
                'processing',
                'succeeded',
                'failed',
                'canceled',
            ])->default('requires_payment_method');
            $table->enum('payment_method', ['card', 'paypal'])->nullable();
            $table->string('failure_reason')->nullable();
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
