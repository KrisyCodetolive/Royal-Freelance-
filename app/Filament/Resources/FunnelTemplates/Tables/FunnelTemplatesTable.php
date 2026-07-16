<?php

namespace App\Filament\Resources\FunnelTemplates\Tables;

use App\Enums\FunnelStatus;
use App\Models\Funnel;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class FunnelTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('template_thumbnail')
                    ->label('Aperçu')
                    ->height(60)
                    ->width(100)
                    ->extraImgAttributes(['class' => 'rounded-lg']),

                TextColumn::make('name')
                    ->label('Nom du Template')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->template_description ? \Str::limit($record->template_description, 50) : null),

                TextColumn::make('template_category')
                    ->label('Catégorie')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'lead_capture' => '📧 Capture de Leads',
                        'webinar' => '🎥 Webinaire',
                        'sales' => '💰 Page de Vente',
                        'thank_you' => '🙏 Remerciement',
                        'coming_soon' => '⏳ Coming Soon',
                        'product_launch' => '🚀 Lancement',
                        'quiz' => '❓ Quiz',
                        'free_training' => '🎓 Formation',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        'lead_capture' => 'info',
                        'sales' => 'success',
                        'webinar' => 'warning',
                        'product_launch' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),

                TextColumn::make('template_uses_count')
                    ->label('Utilisations')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('pages_count')
                    ->label('Pages')
                    ->counts('pages')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('template_category')
                    ->label('Catégorie')
                    ->options([
                        'lead_capture' => 'Capture de Leads',
                        'webinar' => 'Webinaire',
                        'sales' => 'Page de Vente',
                        'thank_you' => 'Page de Remerciement',
                        'coming_soon' => 'Coming Soon',
                        'product_launch' => 'Lancement de Produit',
                        'quiz' => 'Quiz / Sondage',
                        'free_training' => 'Formation Gratuite',
                    ]),
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(FunnelStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('create_funnel')
                    ->label('Créer un tunnel')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form([
                        TextInput::make('funnel_name')
                            ->label('Nom du nouveau tunnel')
                            ->required()
                            ->maxLength(255)
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
                            ->helperText('URL de votre tunnel.'),
                        Select::make('assigned_to')
                            ->label('Assigner à un commercial')
                            ->placeholder('Aucun commercial assigné')
                            ->options(
                                \App\Models\User::role('commercial')
                                    ->where('tenant_id', auth()->user()?->tenant_id)
                                    ->active()
                                    ->get()
                                    ->mapWithKeys(fn($u) => [$u->id => $u->name . ' — ' . $u->email])
                            )
                            ->searchable()
                            ->nullable(),
                        Select::make('offer_id')
                            ->label('Offre associée')
                            ->options(\App\Models\Offer::pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Sélectionner une offre (optionnel)'),
                    ])
                    ->action(function (Funnel $record, array $data) {
                        $funnel = $record->createFromTemplate(
                            $data['funnel_name'],
                            null,
                            $data['offer_id'] ?? null
                        );

                        if (!empty($data['subdomain'])) {
                            $subdomainService = app(\App\Services\SubdomainService::class);
                            $subdomain = strtolower(preg_replace('/[^a-z0-9-]/', '', $data['subdomain']));

                            if ($subdomainService->isAvailable($subdomain, $funnel->id)) {
                                $funnel->subdomain = $subdomain;
                                $funnel->save();
                            }
                        }

                        if (!empty($data['assigned_to'])) {
                            $funnel->assigned_to = $data['assigned_to'];
                            $funnel->save();
                        }

                        Notification::make()
                            ->title('Tunnel créé avec succès')
                            ->body("URL: {$funnel->getPublicUrl()}")
                            ->success()
                            ->duration(10000)
                            ->send();

                        return redirect()->route('filament.admin.resources.funnels.edit', $funnel);
                    }),
                Action::make('preview')
                    ->label('Prévisualiser')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn(Funnel $record) => $record->getPublicUrl())
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('activate')
                        ->label('Activer')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['status' => FunnelStatus::ACTIVE])),
                ]),
            ]);
    }
}
