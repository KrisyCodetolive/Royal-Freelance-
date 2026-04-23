<?php

namespace App\Filament\Resources\CommercialGroups\Pages;

use App\Filament\Resources\CommercialGroups\CommercialGroupResource;
use App\Models\CommercialGroup;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCommercialGroup extends ViewRecord
{
    protected static string $resource = CommercialGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_default_group')
                ->label('Générer groupe par défaut')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->visible(fn() => CommercialGroup::count() === 0 || $this->record->users()->count() === 0)
                ->requiresConfirmation()
                ->modalHeading('Générer un groupe par défaut')
                ->modalDescription('Cette action va créer une structure de groupe de démarrage avec des paramètres par défaut. Voulez-vous continuer ?')
                ->action(function () {
                    // Ajouter des paramètres par défaut si le groupe est vide
                    if (!$this->record->description) {
                        $this->record->update([
                            'description' => 'Groupe commercial par défaut. Ajoutez des commerciaux et assignez des tunnels pour commencer.',
                        ]);
                    }

                    // Assigner tous les tunnels actifs du tenant au groupe
                    $activeFunnels = \App\Models\Funnel::where('tenant_id', $this->record->tenant_id)
                        ->where('is_template', false)
                        ->active()
                        ->pluck('id');

                    if ($activeFunnels->isNotEmpty()) {
                        $syncData = [];
                        foreach ($activeFunnels as $funnelId) {
                            $syncData[$funnelId] = ['can_customize' => true];
                        }
                        $this->record->funnels()->syncWithoutDetaching($syncData);
                    }

                    Notification::make()
                        ->title('Groupe configuré')
                        ->body('Le groupe a été configuré avec les paramètres par défaut et tous les tunnels actifs ont été assignés.')
                        ->success()
                        ->send();

                    $this->refreshFormData(['description']);
                }),

            Action::make('add_all_commercials')
                ->label('Ajouter tous les commerciaux')
                ->icon('heroicon-o-user-plus')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Ajouter tous les commerciaux')
                ->modalDescription('Cette action va ajouter tous les commerciaux du tenant à ce groupe.')
                ->action(function () {
                    $commercialIds = \App\Models\User::where('tenant_id', $this->record->tenant_id)
                        ->role('commercial')
                        ->active()
                        ->pluck('id');

                    $this->record->users()->syncWithoutDetaching($commercialIds);

                    Notification::make()
                        ->title('Commerciaux ajoutés')
                        ->body("{$commercialIds->count()} commercial(aux) ont été ajoutés au groupe.")
                        ->success()
                        ->send();
                }),

            EditAction::make(),
        ];
    }
}
