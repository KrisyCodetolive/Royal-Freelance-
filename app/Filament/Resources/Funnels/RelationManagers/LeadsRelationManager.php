<?php

namespace App\Filament\Resources\Funnels\RelationManagers;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Services\GeolocationService;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\Alignment;
use Filament\Actions; // Using unified Actions namespace

class LeadsRelationManager extends RelationManager
{
    protected static string $relationship = 'leads';

    protected static ?string $title = '👥 Leads Capturés';

    protected static ?string $modelLabel = 'Lead';

    protected static ?string $pluralModelLabel = 'Leads';

    protected static ?string $recordTitleAttribute = 'email';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('Prénom')
                    ->maxLength(255),

                Forms\Components\TextInput::make('last_name')
                    ->label('Nom')
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label('Téléphone')
                    ->maxLength(255),

                Forms\Components\Select::make('status')
                    ->label('Statut')
                    ->options(LeadStatus::class)
                    ->required(),

                Forms\Components\TextInput::make('score')
                    ->label('Score')
                    ->numeric()
                    ->default(0),

                Forms\Components\Select::make('assigned_to')
                    ->label('Assigné à')
                    ->relationship('assignedTo', 'name')
                    ->searchable(),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->getStateUsing(fn($record) => $record->getFullName())
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => match($state) {
                        LeadStatus::COLD => 'gray',
                        LeadStatus::WARM => 'warning',
                        LeadStatus::HOT => 'success',
                        LeadStatus::ULTRA_HOT => 'danger',
                        LeadStatus::CLIENT => 'success',
                        LeadStatus::MEMBER => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->numeric()
                    ->sortable()
                    ->alignment(Alignment::Center)
                    ->color(fn($state) => match(true) {
                        $state >= 50 => 'success',
                        $state >= 25 => 'warning',
                        $state >= 10 => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('country_info')
                    ->label('Localisation')
                    ->getStateUsing(function ($record) {
                        $locationData = $record->custom_fields['location_data'] ?? [];
                        
                        if (empty($locationData)) {
                            return $record->country ?? 'Non défini';
                        }

                        $parts = [];
                        if ($locationData['city'] ?? false) {
                            $parts[] = $locationData['city'];
                        }
                        if ($locationData['country_name'] ?? false) {
                            $parts[] = $locationData['country_name'];
                        }
                        
                        $location = implode(', ', $parts) ?: 'Non défini';
                        
                        // Add flag for African countries
                        if ($locationData['is_african_market'] ?? false) {
                            $flags = [
                                'SN' => '🇸🇳', 'CI' => '🇨🇮', 'CM' => '🇨🇲', 'MA' => '🇲🇦',
                                'TN' => '🇹🇳', 'DZ' => '🇩🇿', 'BF' => '🇧🇫', 'ML' => '🇲🇱',
                                'TG' => '🇹🇬', 'BJ' => '🇧🇯', 'NE' => '🇳🇪', 'GW' => '🇬🇼',
                                'GA' => '🇬🇦', 'CG' => '🇨🇬', 'CF' => '🇨🇫', 'TD' => '🇹🇩',
                                'GQ' => '🇬🇶', 'MG' => '🇲🇬', 'CD' => '🇨🇩', 'RW' => '🇷🇼',
                                'BI' => '🇧🇮', 'NG' => '🇳🇬', 'GH' => '🇬🇭', 'KE' => '🇰🇪',
                            ];
                            $flag = $flags[$locationData['country_code']] ?? '🌍';
                            $location = $flag . ' ' . $location;
                        }
                        
                        return $location;
                    })
                    ->tooltip(function ($record) {
                        $locationData = $record->custom_fields['location_data'] ?? [];
                        if (empty($locationData)) {
                            return 'Données de localisation non disponibles';
                        }
                        
                        $tooltip = [];
                        if ($locationData['target_currency'] ?? false) {
                            $tooltip[] = "Devise: {$locationData['target_currency']}";
                        }
                        if ($locationData['market_potential'] ?? false) {
                            $tooltip[] = "Potentiel marché: {$locationData['market_potential']}/10";
                        }
                        if (!empty($locationData['mobile_money_providers'])) {
                            $tooltip[] = "Mobile Money: " . implode(', ', array_slice($locationData['mobile_money_providers'], 0, 2));
                        }
                        
                        return implode("\n", $tooltip);
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('device_type')
                    ->label('Device')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'mobile' => 'success',
                        'desktop' => 'info',
                        'tablet' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn($state) => match($state) {
                        'mobile' => 'heroicon-o-device-phone-mobile',
                        'desktop' => 'heroicon-o-computer-desktop',
                        'tablet' => 'heroicon-o-device-tablet',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('browser')
                    ->label('Navigateur')
                    ->formatStateUsing(fn($state, $record) => $state ? "$state" : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('total_time_spent')
                    ->label('Temps')
                    ->getStateUsing(fn($record) => $record->events->sum('time_spent_seconds'))
                    ->formatStateUsing(fn($state) => $state ? gmdate('i:s', $state) . 's' : '-')
                    ->description(fn($record) => ($avgScroll = $record->events->avg('scroll_depth_percentage')) ? "📊 " . round($avgScroll) . "%" : null)
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigné à')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('events_count')
                    ->label('Événements')
                    ->counts('events')
                    ->badge()
                    ->color('info')
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Dernière activité')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn($state) => $state && $state->diffInDays() > 7 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Capturé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options(LeadStatus::class)
                    ->multiple(),

                Tables\Filters\SelectFilter::make('device_type')
                    ->label('Device')
                    ->options([
                        'mobile' => '📱 Mobile',
                        'desktop' => '🖥️ Desktop',
                        'tablet' => '📱 Tablet',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigné à')
                    ->relationship('assignedTo', 'name')
                    ->searchable(),

                Tables\Filters\Filter::make('african_market')
                    ->label('Marché Africain')
                    ->query(fn (Builder $query): Builder => $query->whereJsonContains('custom_fields->location_data->is_african_market', true))
                    ->toggle(),

                Tables\Filters\Filter::make('high_score')
                    ->label('Score élevé (25+)')
                    ->query(fn (Builder $query): Builder => $query->where('score', '>=', 25))
                    ->toggle(),

                Tables\Filters\Filter::make('converted')
                    ->label('Convertis')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('converted_at'))
                    ->toggle(),

                Tables\Filters\Filter::make('inactive')
                    ->label('Inactifs (7+ jours)')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->where('last_activity_at', '<', now()->subDays(7))
                          ->orWhere(function ($q2) {
                              $q2->whereNull('last_activity_at')
                                 ->where('created_at', '<', now()->subDays(7));
                          });
                    }))
                    ->toggle(),
            ])
            ->headerActions([
                Actions\Action::make('african_analytics')
                    ->label('📊 Analytics Afrique')
                    ->icon('heroicon-o-document')
                    ->color('success')
                    ->action(function () {
                        // TODO: Show African market analytics modal
                    }),

                Actions\CreateAction::make()
                    ->label('Ajouter Lead'),
            ])
            ->actions([
                Actions\Action::make('view_timeline')
                    ->label('Timeline')
                    ->icon('heroicon-o-clock')
                    ->color('info')
                    ->url(fn($record) => route('filament.admin.resources.leads.view', $record)),

                Actions\Action::make('quick_note')
                    ->label('Note')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->form([
                        Forms\Components\Textarea::make('note')
                            ->label('Note rapide')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data, $record) {
                        $record->addNote($data['note']);
                    }),

                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    
                    Actions\BulkAction::make('assign_to')
                        ->label('Assigner à')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('user_id')
                                ->label('Utilisateur')
                                ->relationship('assignedTo', 'name')
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['assigned_to' => $data['user_id']]);
                            }
                        }),
                        
                        Actions\BulkAction::make('update_status')
                        ->label('Changer statut')
                        ->icon('heroicon-o-flag')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Nouveau statut')
                                ->options(LeadStatus::class)
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading('Aucun lead capturé')
            ->emptyStateDescription('Les leads apparaîtront ici dès que des visiteurs soumettront les formulaires du tunnel.')
            ->emptyStateIcon('heroicon-o-users');
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
