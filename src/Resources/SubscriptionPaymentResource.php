<?php

namespace Mhmadahmd\FilamentSaas\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Mhmadahmd\FilamentSaas\Models\SubscriptionPayment;
use Mhmadahmd\FilamentSaas\Resources\SubscriptionPaymentResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionPaymentResource extends Resource
{
    protected static ?string $model = SubscriptionPayment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\Select::make('subscription_id')
                            ->label('Subscription')
                            ->relationship('subscription', 'name', fn ($query) => $query->whereHas('plan', fn ($q) => $q->where('invoice_period', '>', 0)))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->helperText('Only subscriptions with recurring plans can have payments added.')
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state) {
                                    $subscription = \Mhmadahmd\FilamentSaas\Models\Subscription::find($state);
                                    if ($subscription && $subscription->plan) {
                                        $totalAmount = $subscription->plan->price + $subscription->plan->signup_fee;
                                        $set('amount', $totalAmount);
                                        $set('currency', $subscription->plan->currency);
                                    }
                                }
                            }),
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options(SubscriptionPayment::getPaymentMethods())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === SubscriptionPayment::METHOD_ONLINE) {
                                    $set('status', SubscriptionPayment::STATUS_PENDING);
                                } elseif ($state === SubscriptionPayment::METHOD_CASH) {
                                    $set('status', SubscriptionPayment::STATUS_PAID);
                                }
                            }),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->prefix(fn (Forms\Get $get) => $get('currency') ?? 'USD'),
                        Forms\Components\TextInput::make('currency')
                            ->label('Currency')
                            ->default('USD')
                            ->maxLength(3)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(SubscriptionPayment::getStatuses())
                            ->default(SubscriptionPayment::STATUS_PENDING)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('payment_method') === SubscriptionPayment::METHOD_ONLINE),
                        Forms\Components\TextInput::make('reference_number')
                            ->label('Reference Number')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => in_array($get('payment_method'), [SubscriptionPayment::METHOD_BANK_TRANSFER, SubscriptionPayment::METHOD_CASH])),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Paid At')
                            ->visible(fn (Forms\Get $get) => $get('status') === SubscriptionPayment::STATUS_PAID),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscription.name')
                    ->label('Subscription')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription.plan.name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        SubscriptionPayment::METHOD_CASH => 'success',
                        SubscriptionPayment::METHOD_BANK_TRANSFER => 'info',
                        SubscriptionPayment::METHOD_ONLINE => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => SubscriptionPayment::getPaymentMethods()[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        SubscriptionPayment::STATUS_PAID => 'success',
                        SubscriptionPayment::STATUS_PENDING => 'warning',
                        SubscriptionPayment::STATUS_FAILED => 'danger',
                        SubscriptionPayment::STATUS_REFUNDED => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn ($state) => SubscriptionPayment::getStatuses()[$state] ?? $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference Number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options(SubscriptionPayment::getPaymentMethods()),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(SubscriptionPayment::getStatuses()),
                Tables\Filters\Filter::make('paid_at')
                    ->label('Paid')
                    ->query(fn (Builder $query) => $query->whereNotNull('paid_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->isPending())
                    ->action(fn ($record) => $record->markAsPaid()),
                Tables\Actions\Action::make('mark_as_failed')
                    ->label('Mark as Failed')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => !$record->isFailed())
                    ->action(fn ($record) => $record->markAsFailed()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewSubscriptionPayment::route('/{record}'),
            'index' => Pages\ListSubscriptionPayments::route('/'),
        ];
    }
}

