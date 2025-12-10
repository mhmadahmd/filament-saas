<?php

namespace Mhmadahmd\FilamentSaas\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Mhmadahmd\FilamentSaas\Models\SubscriptionPayment;
use Carbon\Carbon;

class RevenueStatisticsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Revenue Statistics';

    protected function getStats(): array
    {
        $todayRevenue = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PAID)
            ->whereDate('paid_at', today())
            ->sum('amount');

        $yesterdayRevenue = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PAID)
            ->whereDate('paid_at', today()->subDay())
            ->sum('amount');

        $thisMonthRevenue = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PAID)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $lastMonthRevenue = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PAID)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');

        $totalRevenue = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PAID)
            ->sum('amount');

        $pendingRevenue = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PENDING)
            ->sum('amount');

        $todayChange = $yesterdayRevenue > 0
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100
            : ($todayRevenue > 0 ? 100 : 0);

        $monthChange = $lastMonthRevenue > 0
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : ($thisMonthRevenue > 0 ? 100 : 0);

        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = SubscriptionPayment::where('status', SubscriptionPayment::STATUS_PAID)
                ->whereDate('paid_at', today()->subDays($i))
                ->sum('amount');
        }

        return [
            Stat::make('Total Revenue', $this->formatCurrency($totalRevenue))
                ->description('All time')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($last7Days),

            Stat::make('Today\'s Revenue', $this->formatCurrency($todayRevenue))
                ->description(abs(round($todayChange, 1)) . '% ' . ($todayChange >= 0 ? 'increase' : 'decrease') . ' from yesterday')
                ->descriptionIcon($todayChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($todayChange >= 0 ? 'success' : 'danger'),

            Stat::make('This Month', $this->formatCurrency($thisMonthRevenue))
                ->description(abs(round($monthChange, 1)) . '% ' . ($monthChange >= 0 ? 'increase' : 'decrease') . ' from last month')
                ->descriptionIcon($monthChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthChange >= 0 ? 'success' : 'warning'),

            Stat::make('Pending Revenue', $this->formatCurrency($pendingRevenue))
                ->description('Awaiting payment')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
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

