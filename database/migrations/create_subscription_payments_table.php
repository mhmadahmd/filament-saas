<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mhmadahmd\FilamentSaas\Models\SubscriptionPayment;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->string('payment_method')
                ->comment('cash, bank_transfer, online');

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');

            $table->string('status')
                ->default(SubscriptionPayment::STATUS_PENDING)
                ->comment('pending, paid, failed, refunded');

            $table->string('transaction_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();

            $table->dateTime('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['subscription_id', 'status']);
            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
