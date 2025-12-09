<?php

namespace Mhmadahmd\FilamentSaas\Resources\PlanResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Mhmadahmd\FilamentSaas\Resources\PlanResource;

class ListPlans extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
