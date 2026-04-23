<?php

namespace App\Filament\Resources\Funnels\Pages;

use App\Filament\Resources\Funnels\FunnelResource;
use App\Models\CommercialGroup;
use App\Models\User;
use App\Services\TemplateGeneratorService;
use App\Enums\PageType;
use App\Models\Page;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class ViewFunnel extends ViewRecord
{
    protected static string $resource = FunnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_page')
                ->label('Créer une Page')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->form([
                    Forms\Components\TextInput::make('title')
                        ->label('Titre de la page')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                    Forms\Components\Hidden::make('slug'),

                    Forms\Components\Select::make('type')
                        ->label('Type de page')
                        ->options(PageType::class)
                        ->required()
                        ->default(PageType::CAPTURE),

                    Forms\Components\Select::make('page_template')
                        ->label('Modèle de contenu')
                        ->options(TemplateGeneratorService::getPageTemplates())
                        ->searchable()
                        ->preload()
                        ->columnSpanFull()
                        ->helperText('Sélectionnez un modèle pour pré-remplir la page avec des blocs d\'exemple.')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $templateKey = $data['page_template'];
                    unset($data['page_template']);

                    // Calculer le sort_order
                    $data['sort_order'] = $this->record->pages()->max('sort_order') + 1;
                    $data['funnel_id'] = $this->record->id;
                    $data['is_active'] = true;
                    $data['is_required'] = true;

                    if (!isset($data['slug'])) {
                        $data['slug'] = Str::slug($data['title']);
                    }

                    // Créer la page
                    $page = Page::create($data);

                    // Appliquer le template avec les blocs d'exemple
                    if ($templateKey) {
                        app(TemplateGeneratorService::class)->applyPageTemplate($page, $templateKey);
                    }

                    Notification::make()
                        ->title('Page créée avec succès')
                        ->body("La page '{$page->title}' a été créée avec du contenu d'exemple.")
                        ->success()
                        ->send();

                    // Rediriger vers le builder de la page
                    $this->redirect(route('page-builder.edit', ['model' => 'Page', 'modelId' => $page->id]));
                }),

            Action::make('assign_to_commercials')
                ->label('Attribuer')
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->form([
                    Forms\Components\Radio::make('assignment_type')
                        ->label('Type d\'attribution')
                        ->options([
                            'group' => 'Groupe de commerciaux',
                            'individual' => 'Commercial spécifique',
                        ])
                        ->default('group')
                        ->live()
                        ->required(),

                    Forms\Components\Select::make('commercial_group_ids')
                        ->label('Groupes de commerciaux')
                        ->options(fn() => CommercialGroup::where('tenant_id', $this->record->tenant_id)
                            ->active()
                            ->pluck('name', 'id'))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->visible(fn($get) => $get('assignment_type') === 'group')
                        ->required(fn($get) => $get('assignment_type') === 'group'),

                    Forms\Components\Select::make('commercial_ids')
                        ->label('Commerciaux')
                        ->options(fn() => User::where('tenant_id', $this->record->tenant_id)
                            ->role('commercial')
                            ->active()
                            ->pluck('name', 'id'))
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->visible(fn($get) => $get('assignment_type') === 'individual')
                        ->required(fn($get) => $get('assignment_type') === 'individual'),

                    Forms\Components\Toggle::make('can_customize')
                        ->label('Permettre la personnalisation')
                        ->helperText('Les commerciaux pourront personnaliser leurs CTA (boutique, WhatsApp)')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $assignedCount = 0;
                    $canCustomize = $data['can_customize'] ?? true;

                    if ($data['assignment_type'] === 'group' && !empty($data['commercial_group_ids'])) {
                        foreach ($data['commercial_group_ids'] as $groupId) {
                            $group = CommercialGroup::find($groupId);
                            if ($group) {
                                $group->funnels()->syncWithoutDetaching([
                                    $this->record->id => ['can_customize' => $canCustomize]
                                ]);
                                $assignedCount++;
                            }
                        }

                        Notification::make()
                            ->title('Tunnel attribué')
                            ->body("Le tunnel a été attribué à {$assignedCount} groupe(s) de commerciaux.")
                            ->success()
                            ->send();
                    }

                    if ($data['assignment_type'] === 'individual' && !empty($data['commercial_ids'])) {
                        foreach ($data['commercial_ids'] as $userId) {
                            $user = User::find($userId);
                            if ($user) {
                                $user->usableFunnels()->syncWithoutDetaching([
                                    $this->record->id => [
                                        'is_active' => true,
                                        'custom_branding' => null,
                                    ]
                                ]);
                                $assignedCount++;
                            }
                        }

                        Notification::make()
                            ->title('Tunnel attribué')
                            ->body("Le tunnel a été attribué à {$assignedCount} commercial(aux).")
                            ->success()
                            ->send();
                    }
                }),

            Action::make('preview')
                ->label('Prévisualiser')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn() => $this->record->getPublicUrl())
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }

}
