<?php

namespace App\Filament\Resources\FunnelTemplates\Pages;

use App\Filament\Resources\FunnelTemplates\FunnelTemplateResource;
use App\Models\Funnel;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewFunnelTemplate extends ViewRecord
{
    protected static string $resource = FunnelTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('create_funnel')
                ->label('Créer un Tunnel')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->form([
                    TextInput::make('funnel_name')
                        ->label('Nom du nouveau tunnel')
                        ->required()
                        ->maxLength(255)
                        ->default(fn() => $this->record->name . ' - Copie')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set) {
                            $set('subdomain', \Illuminate\Support\Str::slug($state));
                        }),
                    TextInput::make('subdomain')
                        ->label('Sous-domaine')
                        ->required()
                        ->minLength(3)
                        ->maxLength(63)
                        ->prefix('https://')
                        ->suffix('.' . config('app.subdomain_base', 'votredomaine.com'))
                        ->placeholder('mon-tunnel')
                        ->helperText('URL de votre tunnel. Lettres minuscules, chiffres et tirets.')
                        ->default(fn() => \Illuminate\Support\Str::slug($this->record->name . '-copie')),
                    Select::make('offer_id')
                        ->label('Offre associée')
                        ->options(\App\Models\Offer::pluck('name', 'id'))
                        ->searchable()
                        ->placeholder('Sélectionner une offre (optionnel)'),
                ])
                ->action(function (array $data) {
                    /** @var Funnel $template */
                    $template = $this->record;

                    // Créer le funnel depuis le template
                    $funnel = $template->createFromTemplate(
                        $data['funnel_name'],
                        null,
                        $data['offer_id'] ?? null
                    );

                    // Mettre à jour le sous-domaine si fourni
                    if (!empty($data['subdomain'])) {
                        $subdomainService = app(\App\Services\SubdomainService::class);
                        $subdomain = strtolower(preg_replace('/[^a-z0-9-]/', '', $data['subdomain']));

                        if ($subdomainService->isAvailable($subdomain, $funnel->id)) {
                            $funnel->subdomain = $subdomain;
                            $funnel->save();
                        }
                    }

                    Notification::make()
                        ->title('Tunnel créé avec succès!')
                        ->body("Le tunnel '{$funnel->name}' est accessible via : {$funnel->getPublicUrl()}")
                        ->success()
                        ->duration(10000)
                        ->send();

                    return redirect()->route('filament.admin.resources.funnels.edit', $funnel);
                }),
            Actions\Action::make('preview')
                ->label('Prévisualiser')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn() => $this->record->getPublicUrl())
                ->openUrlInNewTab(),
        ];
    }
}
