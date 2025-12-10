<?php

namespace Mhmadahmd\FilamentSaas\Resources;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Mhmadahmd\FilamentSaas\Models\Subscription;
use Mhmadahmd\FilamentSaas\Resources\SubscriptionResource\Pages;
use Mhmadahmd\FilamentSaas\Resources\SubscriptionResource\RelationManagers;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?string $navigationLabel = 'Subscriptions';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscription Information')
                    ->schema([
                        Forms\Components\Select::make('plan_id')
                            ->label('Plan')
                            ->relationship('plan', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $plan = \Mhmadahmd\FilamentSaas\Models\Plan::find($state);
                                    if ($plan) {
                                        $totalAmount = $plan->price + $plan->signup_fee;
                                        $set('payment_amount', $totalAmount);
                                        $set('payment_currency', $plan->currency);
                                    }
                                }
                            }),
                        TranslatableTabs::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Subscription Name')
                                    ->default('main')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('The subscription title (e.g., "main" or "primary")')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Start Date')
                            ->helperText('Optional. If not provided, subscription will start now. Trial and period dates will be calculated automatically based on the plan.')
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated()
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Subscriber')
                    ->schema([
                        Forms\Components\Select::make('subscriber_type')
                            ->label('Subscriber Type')
                            ->options([
                                'App\Models\User' => 'User',
                                'App\Models\Company' => 'Company',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('subscriber_id')
                            ->label('Subscriber')
                            ->options(function (Forms\Get $get) {
                                $type = $get('subscriber_type');
                                if (! $type) {
                                    return [];
                                }

                                return $type::pluck('name', 'id')->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Forms\Get $get) => ! empty($get('subscriber_type'))),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Dates')
                    ->schema([
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Trial Ends At'),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Starts At')
                            ->required(),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Ends At')
                            ->required(),
                        Forms\Components\DateTimePicker::make('cancels_at')
                            ->label('Cancels At'),
                        Forms\Components\DateTimePicker::make('canceled_at')
                            ->label('Canceled At'),
                    ])
                    ->columns(2)
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options(\Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::getPaymentMethods())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state === \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::METHOD_ONLINE) {
                                    $set('payment_status', \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_PENDING);
                                } elseif ($state === \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::METHOD_CASH) {
                                    $set('payment_status', \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_PAID);
                                } else {
                                    $set('payment_status', \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_PENDING);
                                }
                            })
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                        Forms\Components\Select::make('payment_status')
                            ->label('Payment Status')
                            ->options(\Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::getStatuses())
                            ->default(\Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_PENDING)
                            ->required()
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                        Forms\Components\TextInput::make('payment_amount')
                            ->label('Payment Amount')
                            ->numeric()
                            ->required()
                            ->prefix(fn (Forms\Get $get) => $get('payment_currency') ?? 'USD')
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                        Forms\Components\TextInput::make('payment_currency')
                            ->label('Payment Currency')
                            ->default('USD')
                            ->maxLength(3)
                            ->required()
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => 
                                $get('payment_method') === \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::METHOD_ONLINE
                            ),
                        Forms\Components\TextInput::make('reference_number')
                            ->label('Reference Number')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => 
                                in_array($get('payment_method'), [
                                    \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::METHOD_BANK_TRANSFER,
                                    \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::METHOD_CASH
                                ])
                            ),
                        Forms\Components\Textarea::make('payment_notes')
                            ->label('Payment Notes')
                            ->rows(2)
                            ->columnSpanFull()
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                    ])
                    ->columns(2)
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('latestPayment'))
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->active() => 'success',
                        $record->onTrial() => 'warning',
                        $record->canceled() => 'danger',
                        $record->ended() => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn ($record) => match (true) {
                        $record->active() => 'Active',
                        $record->onTrial() => 'Trial',
                        $record->canceled() => 'Canceled',
                        $record->ended() => 'Ended',
                        default => 'Inactive',
                    }),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Starts At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Ends At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payments_count')
                    ->label('Payments')
                    ->counts('payments')
                    ->sortable(),
                Tables\Columns\TextColumn::make('latestPayment.status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_PAID => 'success',
                        \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_PENDING => 'warning',
                        \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::STATUS_FAILED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ? \Mhmadahmd\FilamentSaas\Models\SubscriptionPayment::getStatuses()[$state] ?? $state : 'N/A')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name'),
                Tables\Filters\Filter::make('active')
                    ->label('Active')
                    ->query(fn (Builder $query) => $query->where('ends_at', '>', now())),
                Tables\Filters\Filter::make('trial')
                    ->label('On Trial')
                    ->query(fn (Builder $query) => $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())),
                Tables\Filters\Filter::make('canceled')
                    ->label('Canceled')
                    ->query(fn (Builder $query) => $query->whereNotNull('canceled_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->cancel()),
                Tables\Actions\Action::make('renew')
                    ->label('Renew')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->renew()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
