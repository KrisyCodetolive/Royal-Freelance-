<?php

namespace App\Filament\Resources\EmailSequences\Pages;

use App\Filament\Resources\EmailSequences\EmailSequenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailSequence extends EditRecord
{
    protected static string $resource = EmailSequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Séquence mise à jour';
    }
}
