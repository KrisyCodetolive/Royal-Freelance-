<?php

namespace App\Filament\Resources\FunnelTemplates\Pages;

use App\Filament\Resources\FunnelTemplates\FunnelTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFunnelTemplate extends EditRecord
{
    protected static string $resource = FunnelTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['is_template'] = true;
        return $data;
    }
}
