<?php

namespace App\Filament\Resources\EmailSequences\Pages;

use App\Filament\Concerns\EnforcesTenantQuota;
use App\Filament\Resources\EmailSequences\EmailSequenceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmailSequence extends CreateRecord
{
    use EnforcesTenantQuota;

    protected static string $resource = EmailSequenceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $this->haltIfQuotaReached('mailing_lists', 'listes mailing');

        return parent::handleRecordCreation($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Séquence créée avec succès';
    }
}
