<?php

namespace Mhmadahmd\FilamentSaas\Widgets;

use Filament\Widgets\ChartWidget;
use Mhmadahmd\FilamentSaas\Models\SubscriptionPayment;

class PaymentMethodDistributionWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Payment Method Distribution';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $cash = SubscriptionPayment::where('payment_method', SubscriptionPayment::METHOD_CASH)->count();
        $bankTransfer = SubscriptionPayment::where('payment_method', SubscriptionPayment::METHOD_BANK_TRANSFER)->count();
        $online = SubscriptionPayment::where('payment_method', SubscriptionPayment::METHOD_ONLINE)->count();

        $cashAmount = SubscriptionPayment::where('payment_method', SubscriptionPayment::METHOD_CASH)
            ->where('status', SubscriptionPayment::STATUS_PAID)
            ->sum('amount');
        $bankTransferAmount = SubscriptionPayment::where('payment_method', SubscriptionPayment::METHOD_BANK_TRANSFER)
            ->where('status', SubscriptionPayment::STATUS_PAID)
            ->sum('amount');
        $onlineAmount = SubscriptionPayment::where('payment_method', SubscriptionPayment::METHOD_ONLINE)
            ->where('status', SubscriptionPayment::STATUS_PAID)
            ->sum('amount');

        $total = $cash + $bankTransfer + $online;

        return [
            'datasets' => [
                [
                    'label' => 'Payment Count',
                    'data' => [$cash, $bankTransfer, $online],
                    'backgroundColor' => [
                        'rgb(34, 197, 94)', // green for cash
                        'rgb(59, 130, 246)', // blue for bank transfer
                        'rgb(234, 179, 8)', // yellow for online
                    ],
                ],
            ],
            'labels' => [
                'Cash (' . $cash . ' payments, ' . $this->formatCurrency($cashAmount) . ')',
                'Bank Transfer (' . $bankTransfer . ' payments, ' . $this->formatCurrency($bankTransferAmount) . ')',
                'Online (' . $online . ' payments, ' . $this->formatCurrency($onlineAmount) . ')',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.label + ': ' + context.parsed + ' payments';
                        }",
                    ],
                ],
            ],
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
