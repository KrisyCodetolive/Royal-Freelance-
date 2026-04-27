<?php

namespace App\Filament\Resources\Funnels\Pages;

use App\Filament\Resources\Funnels\FunnelResource;
use App\Services\FunnelTemplateService;
use App\Services\SubdomainService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListFunnels extends ListRecords
{
    protected static string $resource = FunnelResource::class;

    protected function getHeaderActions(): array
    {
        $meta = FunnelTemplateService::getMeta();

        return [
            CreateAction::make()->label('Nouveau Tunnel'),

            Action::make('create_from_template')
                ->label('Créer depuis un template')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->modalHeading('Créer un tunnel depuis un template')
                ->modalDescription('Choisissez un template pour créer votre tunnel avec les pages déjà structurées.')
                ->modalWidth('2xl')
                ->form([
                    Radio::make('template')
                        ->label('Choisir un template')
                        ->options(collect($meta)->map(fn($m) => $m['label'])->toArray())
                        ->descriptions(collect($meta)->map(fn($m) => $m['description'])->toArray())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) use ($meta) {
                            if ($state && isset($meta[$state])) {
                                $set('name', $meta[$state]['label'] . ' — Mon Tunnel');
                                $set('subdomain', Str::slug($meta[$state]['category'] . '-' . rand(100, 999)));
                            }
                        }),

                    TextInput::make('name')
                        ->label('Nom du tunnel')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, callable $set) => $set('subdomain', Str::slug($state))),

                    TextInput::make('subdomain')
                        ->label('Sous-domaine')
                        ->required()
                        ->minLength(3)
                        ->maxLength(63)
                        ->prefix('https://')
                        ->suffix('.' . config('app.subdomain_base', 'votredomaine.com')),

                    Select::make('offer_id')
                        ->label('Offre associée')
                        ->relationship('offer', 'name')
                        ->searchable()
                        ->placeholder('Optionnel'),
                ])
                ->action(function (array $data): void {
                    $funnel = FunnelTemplateService::createFromTemplate(
                        $data['template'],
                        $data['name'],
                        $data['subdomain'],
                    );

                    if ($funnel) {
                        if (!empty($data['offer_id'])) {
                            $funnel->update(['offer_id' => $data['offer_id']]);
                        }

                        Notification::make()
                            ->title('Tunnel créé avec succès')
                            ->body(collect(FunnelTemplateService::getMeta()[$data['template']])
                                ->get('description'))
                            ->success()
                            ->duration(8000)
                            ->send();

                        redirect()->route('filament.admin.resources.funnels.edit', $funnel);
                    }
                })
                ->modalSubmitActionLabel('Créer le tunnel'),
        ];
    }
}
