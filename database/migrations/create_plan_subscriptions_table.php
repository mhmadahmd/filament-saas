<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();

            $table->morphs('subscriber');
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->json('name');
            $table->string('slug');
            $table->json('description')->nullable();
            $table->string('timezone')->nullable();

            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('cancels_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
