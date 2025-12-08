<?php

namespace Mhmadahmd\FilamentSaas\Resources\FeatureResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Mhmadahmd\FilamentSaas\Resources\FeatureResource;

class EditFeature extends EditRecord
{
    protected static string $resource = FeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        dd($data);
    }
}

