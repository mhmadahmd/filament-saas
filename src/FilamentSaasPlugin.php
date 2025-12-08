<?php

namespace Mhmadahmd\FilamentSaas;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Mhmadahmd\FilamentSaas\Resources\PlanResource;
use Mhmadahmd\FilamentSaas\Resources\SubscriptionResource;
use Mhmadahmd\FilamentSaas\Resources\FeatureResource;

class FilamentSaasPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-saas';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                PlanResource::class,
                SubscriptionResource::class,
                FeatureResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
