<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('suspend')
                ->label('Suspendre')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn () => !$this->record->isSuspended())
                ->requiresConfirmation()
                ->modalHeading('Suspendre ce tenant ?')
                ->modalDescription('Coupe immédiatement l\'accès au panel admin pour toute l\'équipe et rend tous les tunnels publics de ce tenant indisponibles (404).')
                ->modalSubmitActionLabel('Suspendre')
                ->action(function () {
                    $this->record->suspend();

                    Notification::make()
                        ->title('Tenant suspendu')
                        ->body("{$this->record->name} n'a plus accès au panel, ses tunnels publics sont désormais indisponibles.")
                        ->warning()
                        ->send();
                }),

            Action::make('reactivate')
                ->label('Réactiver')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->isSuspended())
                ->requiresConfirmation()
                ->modalHeading('Réactiver ce tenant ?')
                ->modalDescription('Rétablit l\'accès au panel admin et aux tunnels publics de ce tenant.')
                ->modalSubmitActionLabel('Réactiver')
                ->action(function () {
                    $this->record->reactivate();

                    Notification::make()
                        ->title('Tenant réactivé')
                        ->success()
                        ->send();
                }),
        ];
    }
}
