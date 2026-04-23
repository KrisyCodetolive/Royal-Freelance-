<?php

namespace App\Filament\Resources\CommercialGroups\Pages;

use App\Filament\Resources\CommercialGroups\CommercialGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommercialGroups extends ListRecords
{
    protected static string $resource = CommercialGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
