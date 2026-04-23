<?php

namespace App\Filament\Resources\EmailSequences\Pages;

use App\Filament\Resources\EmailSequences\EmailSequenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailSequences extends ListRecords
{
    protected static string $resource = EmailSequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouvelle Séquence')
                ->icon('heroicon-o-plus'),
        ];
    }
}
