<?php

namespace Mhmadahmd\FilamentSaas\Resources\FeatureResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Mhmadahmd\FilamentSaas\Resources\FeatureResource;

class ListFeatures extends ListRecords
{
    protected static string $resource = FeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

