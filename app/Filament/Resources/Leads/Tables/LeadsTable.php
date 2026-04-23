<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use App\Models\EmailSequence;
use App\Models\Lead;
use App\Services\LeadService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Collection;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->getStateUsing(fn($record) => $record->getFullName())
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->email),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->url(fn($record) => "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->phone ?? ''))
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),

                TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->color(fn($record, $state) => $record->tags->where('name', $state)->first()?->color ?? 'gray')
                    ->separator(',')
                    ->limitList(2)
                    ->toggleable(),

                TextColumn::make('score')
                    ->label('Score')
                    ->numeric()
                    ->sortable()
                    ->color(fn($state) => match (true) {
                        $state >= 61 => 'danger',
                        $state >= 31 => 'warning',
                        $state >= 11 => 'info',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match (true) {
                        $state >= 61 => 'heroicon-o-fire',
                        $state >= 31 => 'heroicon-o-arrow-trending-up',
                        default => 'heroicon-o-minus',
                    }),

                TextColumn::make('funnel.name')
                    ->label('Tunnel')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->toggleable(),

                TextColumn::make('device_type')
                    ->label('Device')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'mobile' => 'success',
                        'desktop' => 'info',
                        'tablet' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match ($state) {
                        'mobile' => 'heroicon-o-device-phone-mobile',
                        'desktop' => 'heroicon-o-computer-desktop',
                        'tablet' => 'heroicon-o-device-tablet',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('browser')
                    ->label('Navigateur')
                    ->formatStateUsing(fn($state, $record) => $state ? "$state " . ($record->browser_version ? substr($record->browser_version, 0, 4) : '') : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('os')
                    ->label('OS')
                    ->formatStateUsing(fn($state, $record) => $state ? "$state " . ($record->os_version ?? '') : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country')
                    ->label('Pays')
                    ->formatStateUsing(fn($state) => $state ? (\Locale::getDisplayRegion("-{$state}", 'fr') ?: $state) : '-')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('city')
                    ->label('Ville')
                    ->description(fn($record) => $record->timezone)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('screen_resolution')
                    ->label('Résolution')
                    ->icon('heroicon-o-computer-desktop')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('language')
                    ->label('Langue')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('source')
                    ->label('Source')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('assignedTo.name')
                    ->label('Assigné à')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('broughtBy.name')
                    ->label('Commercial')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_time_spent')
                    ->label('Temps Total')
                    ->getStateUsing(fn($record) => $record->events->sum('time_spent_seconds'))
                    ->formatStateUsing(fn($state) => $state ? gmdate('H:i:s', $state) : '-')
                    ->description(fn($record) => ($avgScroll = $record->events->avg('scroll_depth_percentage')) ? "📊 Scroll: " . round($avgScroll) . "%" : null)
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('events_count')
                    ->label('Événements')
                    ->counts('events')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_activity_at')
                    ->label('Activité')
                    ->since()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(LeadStatus::class)
                    ->multiple(),
                SelectFilter::make('tags')
                    ->label('Tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                SelectFilter::make('device_type')
                    ->label('Device')
                    ->options([
                        'mobile' => '📱 Mobile',
                        'desktop' => '🖥️ Desktop',
                        'tablet' => '📱 Tablet',
                    ])
                    ->multiple(),
                SelectFilter::make('country')
                    ->label('Pays')
                    ->options(
                        fn() => \App\Models\Lead::query()
                            ->whereNotNull('country')
                            ->distinct()
                            ->pluck('country', 'country')
                            ->mapWithKeys(fn($code) => [$code => \Locale::getDisplayRegion("-{$code}", 'fr') ?: $code])
                            ->sort()
                    )
                    ->multiple()
                    ->searchable(),
                SelectFilter::make('funnel_id')
                    ->label('Tunnel')
                    ->relationship('funnel', 'name'),
                SelectFilter::make('assigned_to')
                    ->label('Assigné à')
                    ->relationship('assignedTo', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->label('Voir'),
                EditAction::make()->label('Modifier'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assign_tags')
                        ->label('Assigner Tags')
                        ->icon('heroicon-o-tag')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('tags')
                                ->label('Tags à assigner')
                                ->multiple()
                                ->relationship('tags', 'name')
                                ->preload()
                                ->required()
                                ->helperText('Les tags seront ajoutés aux leads sélectionnés sans supprimer les tags existants'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->tags()->syncWithoutDetaching($data['tags']);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Tags assignés avec succès'),

                    BulkAction::make('remove_tags')
                        ->label('Retirer Tags')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->form([
                            Forms\Components\Select::make('tags')
                                ->label('Tags à retirer')
                                ->multiple()
                                ->options(function (Collection $records) {
                                    // Récupérer tous les tags des leads sélectionnés
                                    return $records->pluck('tags')->flatten()->unique('id')->pluck('name', 'id');
                                })
                                ->required()
                                ->helperText('Seuls les tags communs aux leads sélectionnés sont affichés'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->tags()->detach($data['tags']);
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Tags retirés avec succès'),

                    BulkAction::make('start_sequence')
                        ->label('Démarrer Séquence Email')
                        ->icon('heroicon-o-envelope')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('sequence_id')
                                ->label('Séquence Email')
                                ->options(EmailSequence::where('status', 'active')->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->helperText('Les leads seront inscrits à cette séquence automatiquement'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $sequence = EmailSequence::find($data['sequence_id']);
                            $count = 0;

                            foreach ($records as $lead) {
                                // Vérifier si le lead n'est pas déjà inscrit
                                if (!$sequence->subscriptions()->where('lead_id', $lead->id)->exists()) {
                                    $sequence->subscribeLead($lead);
                                    $count++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("$count leads inscrits à la séquence")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Séquence démarrée avec succès'),

                    BulkAction::make('merge_leads')
                        ->label('Fusionner les doublons')
                        ->icon('heroicon-o-arrows-pointing-in')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Fusionner ces leads ?')
                        ->modalDescription('Le lead avec le score le plus élevé sera conservé. Les données des autres leads seront transférées puis supprimées (soft-delete).')
                        ->modalSubmitActionLabel('Fusionner')
                        ->action(function (Collection $records) {
                            if ($records->count() < 2) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Sélectionnez au moins 2 leads à fusionner')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $sorted = $records->sortByDesc('score');
                            $primary = $sorted->first();
                            $secondaryIds = $sorted->slice(1)->pluck('id')->toArray();

                            $leadService = app(LeadService::class);
                            $merged = $leadService->mergeLeads($primary, $secondaryIds);

                            \Filament\Notifications\Notification::make()
                                ->title('🔀 ' . count($secondaryIds) . ' lead(s) fusionné(s) vers #' . $merged->id)
                                ->body('Score final : ' . $merged->score . ' | Statut : ' . $merged->status->label())
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()->label('Supprimer'),
                    ForceDeleteBulkAction::make()->label('Supprimer définitivement'),
                    RestoreBulkAction::make()->label('Restaurer'),
                ]),
            ])
            ->defaultSort('last_activity_at', 'desc')
            ->striped();
    }
}
