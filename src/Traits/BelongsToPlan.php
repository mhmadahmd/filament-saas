<?php

declare(strict_types=1);

namespace Mhmadahmd\FilamentSaas\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mhmadahmd\FilamentSaas\Models\Plan;

trait BelongsToPlan
{
    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('saas.models.plan', Plan::class), 'plan_id', 'id', 'plan');
    }

    public function scopeByPlanId(Builder $builder, int $planId): Builder
    {
        return $builder->where('plan_id', $planId);
    }
}
