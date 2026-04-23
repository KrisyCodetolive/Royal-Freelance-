<?php

namespace App\Filament\Resources\FunnelTemplates\Pages;

use App\Filament\Resources\FunnelTemplates\FunnelTemplateResource;
use App\Services\TemplateGeneratorService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListFunnelTemplates extends ListRecords
{
    protected static string $resource = FunnelTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_defaults')
                ->label('Générer Tous les Templates')
                ->icon('heroicon-o-sparkles')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Générer les templates par défaut')
                ->modalDescription('Cette action va créer les templates de base avec pages et blocs pré-configurés. Les templates existants ne seront pas modifiés.')
                ->modalSubmitActionLabel('Générer')
                ->action(function () {
                    $service = new TemplateGeneratorService();
                    $templates = $service->generateAllDefaults();

                    Notification::make()
                        ->title('Templates générés avec succès!')
                        ->body(count($templates) . ' templates ont été créés avec leurs pages et blocs.')
                        ->success()
                        ->duration(5000)
                        ->send();
                }),

            Action::make('generate_lead_capture')
                ->label('Lead Capture')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->action(function () {
                    $service = new TemplateGeneratorService();
                    $template = $service->generateByCategory('lead_capture');

                    if (!$template) {
                        Notification::make()
                            ->title('Erreur')
                            ->body('Template lead_capture introuvable.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('Template créé!')
                        ->body("Le template '{$template->name}' a été créé.")
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.funnel-templates.edit', $template);
                }),

            // Nouveaux templates complets
            Action::make('generate_online_training')
                ->label('🎓 Formation')
                ->icon('heroicon-o-academic-cap')
                ->color('indigo')
                ->action(function () {
                    $service = new TemplateGeneratorService();
                    $template = $service->generateByCategory('formation');

                    if (!$template) {
                        Notification::make()
                            ->title('Erreur')
                            ->body('Template formation introuvable.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('Template Formation créé!')
                        ->body("Le template '{$template->name}' avec 5 pages a été créé.")
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.funnel-templates.edit', $template);
                }),

            Action::make('generate_digital_book')
                ->label('📚 Livre')
                ->icon('heroicon-o-book-open')
                ->color('purple')
                ->action(function () {
                    $service = new TemplateGeneratorService();
                    $template = $service->generateByCategory('digital_product');

                    if (!$template) {
                        Notification::make()
                            ->title('Erreur')
                            ->body('Template digital_product introuvable.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('Template Livre créé!')
                        ->body("Le template '{$template->name}' avec 4 pages a été créé.")
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.funnel-templates.edit', $template);
                }),

            Action::make('generate_coaching_program')
                ->label('🎯 Coaching')
                ->icon('heroicon-o-user-group')
                ->color('amber')
                ->action(function () {
                    $service = new TemplateGeneratorService();
                    $template = $service->generateByCategory('coaching');

                    if (!$template) {
                        Notification::make()
                            ->title('Erreur')
                            ->body('Template coaching introuvable.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('Template Coaching créé!')
                        ->body("Le template '{$template->name}' avec quiz et 5 pages a été créé.")
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.funnel-templates.edit', $template);
                }),

            Action::make('generate_mlm_community')
                ->label('🤝 MLM & Communauté')
                ->icon('heroicon-o-sparkles')
                ->color('emerald')
                ->action(function () {
                    $service = new TemplateGeneratorService();
                    $template = $service->generateByCategory('mlm_community');

                    if (!$template) {
                        Notification::make()
                            ->title('Erreur')
                            ->body('Template mlm_community introuvable.')
                            ->danger()
                            ->send();
                        return;
                    }

                    Notification::make()
                        ->title('Template MLM créé!')
                        ->body("Le template '{$template->name}' avec 4 pages a été créé.")
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.funnel-templates.edit', $template);
                }),

            CreateAction::make()
                ->label('Template Vide')
                ->icon('heroicon-o-plus'),
        ];
    }
}
