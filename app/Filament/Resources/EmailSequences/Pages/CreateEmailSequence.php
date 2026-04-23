<?php

namespace App\Filament\Resources\EmailSequences\Pages;

use App\Filament\Resources\EmailSequences\EmailSequenceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailSequence extends CreateRecord
{
    protected static string $resource = EmailSequenceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Séquence créée avec succès';
    }
}
