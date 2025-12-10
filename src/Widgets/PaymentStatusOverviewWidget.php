<?php

namespace Mhmadahmd\FilamentSaas\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Mhmadahmd\FilamentSaas\Models\SubscriptionPayment;

class PaymentStatusOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Payment Status Overview';

    protected function getStats(): array
    {
        $paid = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PAID)->count();
        $pending = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PENDING)->count();
        $failed = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_FAILED)->count();
        $refunded = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_REFUNDED)->count();
        $total = SubscriptionPayment::count();

        $paidAmount = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PAID)->sum('amount');
        $pendingAmount = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PENDING)->sum('amount');
        $failedAmount = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_FAILED)->sum('amount');
        $refundedAmount = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_REFUNDED)->sum('amount');

        $paidPercentage = $total > 0 ? round(($paid / $total) * 100, 1) : 0;
        $pendingPercentage = $total > 0 ? round(($pending / $total) * 100, 1) : 0;

        return [
            Stat::make('Paid Payments', $paid)
                ->description($this->formatCurrency($paidAmount) . ' • ' . $paidPercentage . '% of total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Pending Payments', $pending)
                ->description($this->formatCurrency($pendingAmount) . ' • ' . $pendingPercentage . '% of total')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Failed Payments', $failed)
                ->description($this->formatCurrency($failedAmount))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Refunded Payments', $refunded)
                ->description($this->formatCurrency($refundedAmount))
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('gray'),
        ];
    }

    protected function formatCurrency(float $amount): string
    {
        $currency = config('saas.default_currency', 'USD');
        $symbol = $this->getCurrencySymbol($currency);

        return $symbol . number_format($amount, 2);
    }

    protected function getCurrencySymbol(string $currency): string
    {
        return match ($currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            default => $currency . ' ',
        };
    }
}

