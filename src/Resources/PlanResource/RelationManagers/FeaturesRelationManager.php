<?php

namespace Mhmadahmd\FilamentSaas\Resources\PlanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;

class FeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'features';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Feature Information')
                    ->schema([
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
                            ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule) {
                                return $rule->where('plan_id', $this->getOwnerRecord()->id);
                            })
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sort Order')
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }
}

