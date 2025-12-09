<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_usage', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('features')->cascadeOnDelete();
            $table->unsignedSmallInteger('used');
            $table->string('timezone')->nullable();

            $table->dateTime('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usage');
    }
};
