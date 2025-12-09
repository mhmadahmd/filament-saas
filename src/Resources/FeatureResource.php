<?php

namespace Mhmadahmd\FilamentSaas\Resources;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Mhmadahmd\FilamentSaas\Models\Feature;
use Mhmadahmd\FilamentSaas\Resources\FeatureResource\Pages;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Subscriptions';

    protected static ?string $navigationLabel = 'Features';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feature Information')
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
                                    ->label('Feature Name')
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

                Forms\Components\Section::make('Feature Value')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('Value')
                            ->helperText('Use "true" for boolean features, or a number for countable features')
                            ->required()
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Reset Settings')
                    ->schema([
                        Forms\Components\TextInput::make('resettable_period')
                            ->label('Resettable Period')
                            ->numeric()
                            ->default(0)
                            ->helperText('Set to 0 to disable reset'),
                        Forms\Components\Select::make('resettable_interval')
                            ->label('Resettable Interval')
                            ->options([
                                'day' => 'Day',
                                'week' => 'Week',
                                'month' => 'Month',
                                'year' => 'Year',
                            ])
                            ->default('month'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Feature Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->sortable(),
                Tables\Columns\TextColumn::make('resettable_period')
                    ->label('Resettable')
                    ->formatStateUsing(fn ($record) => $record->resettable_period > 0
                        ? $record->resettable_period . ' ' . $record->resettable_interval
                        : 'Never')
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
                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}
