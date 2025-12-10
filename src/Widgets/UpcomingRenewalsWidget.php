<?php

namespace Mhmadahmd\FilamentSaas\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Mhmadahmd\FilamentSaas\Models\Subscription;

class UpcomingRenewalsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Subscription::query()
                    ->where('ends_at', '>', Carbon::now())
                    ->where('ends_at', '<=', Carbon::now()->addDays(7))
                    ->whereNull('canceled_at')
                    ->with(['plan', 'subscriber', 'latestPayment'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Subscription')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscriber.name')
                    ->label('Subscriber')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.price')
                    ->label('Amount')
                    ->money(fn ($record) => $record->plan->currency ?? 'USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Renews At')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        $record->ends_at->isToday() => 'danger',
                        $record->ends_at->isTomorrow() => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('days_until_renewal')
                    ->label('Days Left')
                    ->getStateUsing(function ($record) {
                        $days = Carbon::now()->diffInDays($record->ends_at, false);

                        return $days > 0 ? $days . ' days' : 'Expired';
                    })
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->ends_at->isToday() => 'danger',
                        Carbon::now()->diffInDays($record->ends_at) <= 3 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('latestPayment.status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_PAID => 'success',
                        \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_PENDING => 'warning',
                        \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_FAILED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(
                        fn ($state) => $state
                        ? \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::getStatuses()[$state] ?? $state
                        : 'N/A'
                    )
                    ->toggleable(),
            ])
            ->defaultSort('ends_at', 'asc')
            ->paginated(false)
            ->heading('Subscriptions Renewing in the Next 7 Days');
    }
}
