<?php

namespace Mhmadahmd\FilamentSaas\Resources\SubscriptionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Mhmadahmd\FilamentSaas\Models\SubscriptionPayment;
use Illuminate\Validation\ValidationException;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'transaction_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Information')
                    ->schema([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('transaction_id')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->badge()
                    ->formatStateUsing(fn ($state) => SubscriptionPayment::getPaymentMethods()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        SubscriptionPayment::METHOD_CASH => 'success',
                        SubscriptionPayment::METHOD_BANK_TRANSFER => 'info',
                        SubscriptionPayment::METHOD_ONLINE => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => SubscriptionPayment::getStatuses()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        SubscriptionPayment::STATUS_PAID => 'success',
                        SubscriptionPayment::STATUS_PENDING => 'warning',
                        SubscriptionPayment::STATUS_FAILED => 'danger',
                        SubscriptionPayment::STATUS_REFUNDED => 'gray',
                        default => 'primary',
                    })
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
            ])
            ->defaultSort('created_at', 'desc');
    }
}

