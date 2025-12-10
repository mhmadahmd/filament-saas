<?php

namespace Mhmadahmd\FilamentSaas;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Mhmadahmd\FilamentSaas\Resources\FeatureResource;
use Mhmadahmd\FilamentSaas\Resources\PlanResource;
use Mhmadahmd\FilamentSaas\Resources\SubscriptionPaymentResource;
use Mhmadahmd\FilamentSaas\Resources\SubscriptionResource;
use Mhmadahmd\FilamentSaas\Widgets\PaymentMethodDistributionWidget;
use Mhmadahmd\FilamentSaas\Widgets\PaymentStatusOverviewWidget;
use Mhmadahmd\FilamentSaas\Widgets\RevenueStatisticsWidget;
use Mhmadahmd\FilamentSaas\Widgets\UpcomingRenewalsWidget;

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
                SubscriptionPaymentResource::class,
                FeatureResource::class,
            ])
            ->widgets([
                RevenueStatisticsWidget::class,
                PaymentStatusOverviewWidget::class,
                UpcomingRenewalsWidget::class,
                PaymentMethodDistributionWidget::class,
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
