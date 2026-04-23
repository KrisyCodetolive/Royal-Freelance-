<?php

namespace App\Filament\Resources\Funnels\RelationManagers;

use App\Enums\EmailSequenceStatus;
use App\Enums\EmailSequenceTrigger;
use App\Models\EmailSequence;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\Alignment;
use Filament\Actions; // Using unified Actions namespace

class EmailSequencesRelationManager extends RelationManager
{
    protected static string $relationship = 'emailSequences';

    protected static ?string $title = '📧 Séquences Email';

    protected static ?string $modelLabel = 'Séquence';

    protected static ?string $pluralModelLabel = 'Séquences Email';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom de la séquence')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('ex: Bienvenue nouveaux prospects'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->placeholder('Décrivez l\'objectif de cette séquence email'),

                Forms\Components\Select::make('trigger')
                    ->label('Déclencheur')
                    ->options(EmailSequenceTrigger::class)
                    ->required()
                    ->live()
                    ->helperText(fn($state) => $state ? $state->description() : 'Choisissez quand cette séquence doit se déclencher'),

                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options(EmailSequenceStatus::class)
                    ->required()
                    ->default(EmailSequenceStatus::DRAFT),

                Forms\Components\KeyValue::make('trigger_conditions')
                    ->label('Conditions du déclencheur')
                    ->visible(fn($get) => $get('trigger') && $get('trigger')->requiredConditions())
                    ->helperText(function ($get) {
                        $trigger = $get('trigger');
                        if (!$trigger) return null;
                        
                        $conditions = $trigger->requiredConditions();
                        if (empty($conditions)) return 'Aucune condition supplémentaire requise';
                        
                        $help = 'Conditions requises: ' . implode(', ', $conditions);
                        
                        return $help;
                    }),

                Forms\Components\KeyValue::make('settings')
                    ->label('Paramètres avancés')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->description),

                Tables\Columns\TextColumn::make('trigger')
                    ->label('Déclencheur')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => match($state) {
                        EmailSequenceTrigger::FORM_SUBMIT => 'success',
                        EmailSequenceTrigger::SCORE_THRESHOLD => 'warning',
                        EmailSequenceTrigger::PAGE_VIEW => 'info',
                        EmailSequenceTrigger::TAG_ASSIGNED => 'primary',
                        EmailSequenceTrigger::INACTIVITY => 'danger',
                        EmailSequenceTrigger::MANUAL => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn($state) => $state->icon())
                    ->tooltip(fn($state) => $state->description()),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => $state->color())
                    ->icon(fn($state) => $state->icon())
                    ->sortable(),

                Tables\Columns\TextColumn::make('emails_count')
                    ->label('Emails')
                    ->counts('emails')
                    ->badge()
                    ->color('info')
                    ->alignment(Alignment::Center)
                    ->sortable(),

                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Abonnés')
                    ->counts('subscriptions')
                    ->badge()
                    ->color('primary')
                    ->alignment(Alignment::Center)
                    ->sortable(),

                Tables\Columns\TextColumn::make('open_rate')
                    ->label('Taux d\'ouverture')
                    ->getStateUsing(fn($record) => $record->getOpenRate() . '%')
                    ->color(fn($record) => match(true) {
                        $record->getOpenRate() >= 25 => 'success',
                        $record->getOpenRate() >= 15 => 'warning',
                        $record->getOpenRate() >= 5 => 'info',
                        default => 'gray',
                    })
                    ->alignment(Alignment::Center),

                Tables\Columns\TextColumn::make('click_rate')
                    ->label('Taux de clic')
                    ->getStateUsing(fn($record) => $record->getClickRate() . '%')
                    ->color(fn($record) => match(true) {
                        $record->getClickRate() >= 5 => 'success',
                        $record->getClickRate() >= 2 => 'warning',
                        $record->getClickRate() >= 1 => 'info',
                        default => 'gray',
                    })
                    ->alignment(Alignment::Center),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('trigger')
                    ->label('Déclencheur')
                    ->options(EmailSequenceTrigger::class),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(EmailSequenceStatus::class),

                Tables\Filters\Filter::make('active')
                    ->label('Actives seulement')
                    ->query(fn (Builder $query): Builder => $query->where('status', EmailSequenceStatus::ACTIVE))
                    ->toggle(),

                Tables\Filters\Filter::make('has_subscribers')
                    ->label('Avec abonnés')
                    ->query(fn (Builder $query): Builder => $query->has('subscriptions'))
                    ->toggle(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nouvelle Séquence')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['tenant_id'] = auth()->user()->tenant_id;
                        return $data;
                    }),

                Actions\Action::make('create_default_sequences')
                    ->label('🚀 Séquences par défaut')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Créer les séquences par défaut')
                    ->modalDescription('Cela va créer 3 séquences email prêtes à l\'emploi : Bienvenue, Nurturing et Réengagement.')
                    ->action(function () {
                        $emailService = app(\App\Services\EmailService::class);
                        $funnel = $this->getOwnerRecord();
                        
                        // Create default sequences for this funnel's tenant
                        $emailService->createDefaultSequences($funnel->tenant);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Séquences créées!')
                            ->body('Les séquences par défaut ont été créées avec succès.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Actions\Action::make('manage_emails')
                    ->label('Emails')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->action(function ($record) {
                        \Filament\Notifications\Notification::make()
                            ->title('Gestion des emails')
                            ->body("La séquence '{$record->name}' contient {$record->emails()->count()} emails.")
                            ->info()
                            ->send();
                    }),

                Actions\Action::make('view_stats')
                    ->label('Stats')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->modalHeading(fn($record) => "Statistiques : {$record->name}")
                    ->modalContent(function ($record) {
                        $stats = $record->stats ?? [];
                        $content = "<div class='space-y-4'>";
                        $content .= "<div class='grid grid-cols-2 gap-4'>";
                        $content .= "<div class='p-4 bg-blue-50 rounded-lg'><h4 class='font-medium text-blue-900'>Emails envoyés</h4><p class='text-2xl font-bold text-blue-600'>" . ($stats['sent'] ?? 0) . "</p></div>";
                        $content .= "<div class='p-4 bg-green-50 rounded-lg'><h4 class='font-medium text-green-900'>Taux d'ouverture</h4><p class='text-2xl font-bold text-green-600'>" . $record->getOpenRate() . "%</p></div>";
                        $content .= "<div class='p-4 bg-purple-50 rounded-lg'><h4 class='font-medium text-purple-900'>Taux de clic</h4><p class='text-2xl font-bold text-purple-600'>" . $record->getClickRate() . "%</p></div>";
                        $content .= "<div class='p-4 bg-orange-50 rounded-lg'><h4 class='font-medium text-orange-900'>Abonnés actifs</h4><p class='text-2xl font-bold text-orange-600'>" . $record->getTotalSubscribers() . "</p></div>";
                        $content .= "</div></div>";
                        return new \Illuminate\Support\HtmlString($content);
                    })
                    ->modalWidth('4xl'),

                Actions\Action::make('duplicate')
                    ->label('Dupliquer')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function ($record) {
                        $newSequence = $record->replicate();
                        $newSequence->name = $record->name . ' (copie)';
                        $newSequence->status = EmailSequenceStatus::DRAFT;
                        $newSequence->save();
                        
                        // Duplicate emails
                        foreach ($record->emails as $email) {
                            $newEmail = $email->replicate();
                            $newEmail->email_sequence_id = $newSequence->id;
                            $newEmail->save();
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Séquence dupliquée!')
                            ->body("La séquence '{$newSequence->name}' a été créée.")
                            ->success()
                            ->send();
                    }),

                Actions\EditAction::make(),

                Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Attention: Supprimer cette séquence supprimera aussi tous ses emails et abonnements.'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),

                    Actions\BulkAction::make('activate')
                        ->label('Activer')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => EmailSequenceStatus::ACTIVE]);
                            }
                        }),

                    Actions\BulkAction::make('pause')
                        ->label('Mettre en pause')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['status' => EmailSequenceStatus::PAUSED]);
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading('Aucune séquence email')
            ->emptyStateDescription('Créez votre première séquence email pour automatiser le nurturing de vos leads.')
            ->emptyStateActions([
                Actions\CreateAction::make()
                    ->label('Créer une séquence')
                    ->icon('heroicon-o-plus'),
            ])
            ->emptyStateIcon('heroicon-o-envelope');
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        
        if ($query !== null) {
            return $query->with(['emails', 'subscriptions']);
        }
        
        // Fallback if parent returns null
        return EmailSequence::query()->with(['emails', 'subscriptions']);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
