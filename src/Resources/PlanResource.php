<?php

namespace Mhmadahmd\FilamentSaas\Resources;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Mhmadahmd\FilamentSaas\Models\Plan;
use Mhmadahmd\FilamentSaas\Resources\PlanResource\Pages;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?string $navigationLabel = 'Plans';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Information')
                    ->schema([
                        TranslatableTabs::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Plan Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('signup_fee')
                            ->label('Signup Fee')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('currency')
                            ->label('Currency')
                            ->default('USD')
                            ->maxLength(3)
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Billing Period')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_period')
                            ->label('Invoice Period')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\Select::make('invoice_interval')
                            ->label('Invoice Interval')
                            ->options([
                                'day' => 'Day',
                                'week' => 'Week',
                                'month' => 'Month',
                                'year' => 'Year',
                            ])
                            ->default('month')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Trial Period')
                    ->schema([
                        Forms\Components\TextInput::make('trial_period')
                            ->label('Trial Period')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('trial_interval')
                            ->label('Trial Interval')
                            ->options([
                                'day' => 'Day',
                                'week' => 'Week',
                                'month' => 'Month',
                                'year' => 'Year',
                            ])
                            ->default('day'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Grace Period')
                    ->schema([
                        Forms\Components\TextInput::make('grace_period')
                            ->label('Grace Period')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('grace_interval')
                            ->label('Grace Interval')
                            ->options([
                                'day' => 'Day',
                                'week' => 'Week',
                                'month' => 'Month',
                                'year' => 'Year',
                            ])
                            ->default('day'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                        Forms\Components\TextInput::make('active_subscribers_limit')
                            ->label('Active Subscribers Limit')
                            ->numeric()
                            ->default(0)
                            ->helperText('0 = unlimited'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Plan Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_period')
                    ->label('Billing Period')
                    ->formatStateUsing(fn ($record) => $record->invoice_period . ' ' . $record->invoice_interval)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Subscribers')
                    ->counts('subscriptions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
