<?php

namespace App\Filament\Resources\CommercialGroups\Pages;

use App\Filament\Resources\CommercialGroups\CommercialGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCommercialGroup extends EditRecord
{
    protected static string $resource = CommercialGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
