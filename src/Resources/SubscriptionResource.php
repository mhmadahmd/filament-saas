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
                            ->required(),
                        TranslatableTabs::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Subscription Name')
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
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
