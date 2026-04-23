<?php

namespace App\Filament\Resources\FunnelTemplates\Pages;

use App\Filament\Resources\FunnelTemplates\FunnelTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFunnelTemplate extends CreateRecord
{
    protected static string $resource = FunnelTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_template'] = true;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
