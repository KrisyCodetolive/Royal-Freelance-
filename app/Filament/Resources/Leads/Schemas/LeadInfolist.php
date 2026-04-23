<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Lead')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Informations')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Section::make('Identité')
                                            ->schema([
                                                TextEntry::make('full_name')
                                                    ->label('Nom complet')
                                                    ->state(fn($record) => $record->getFullName())
                                                    ->size(10)
                                                    ->weight('bold')
                                                    ->copyable()
                                                    ->icon('heroicon-o-user'),
                                                TextEntry::make('email')
                                                    ->label('Email')
                                                    ->copyable()
                                                    ->icon('heroicon-o-envelope')
                                                    ->url(fn($record) => "mailto:{$record->email}"),
                                                TextEntry::make('phone')
                                                    ->label('Téléphone')
                                                    ->copyable()
                                                    ->icon('heroicon-o-phone')
                                                    ->url(fn($record) => "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->phone ?? ''))
                                                    ->openUrlInNewTab(),
                                            ])
                                            ->columnSpan(1),

                                        Section::make('Statut & Score')
                                            ->schema([
                                                TextEntry::make('status')
                                                    ->label('Statut')
                                                    ->badge(),
                                                TextEntry::make('score')
                                                    ->label('Score')
                                                    ->numeric()
                                                    ->suffix(' pts')
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
                                                TextEntry::make('funnel.name')
                                                    ->label('Tunnel d\'origine')
                                                    ->badge()
                                                    ->color('primary'),
                                            ])
                                            ->columnSpan(1),
                                    ]),
                                Section::make('Assignation')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('assignedTo.name')
                                                ->label('Assigné à')
                                                ->default('Non assigné')
                                                ->icon('heroicon-o-user'),
                                            TextEntry::make('broughtBy.name')
                                                ->label('Apporté par')
                                                ->default('Aucun')
                                                ->icon('heroicon-o-user-circle'),
                                        ]),
                                    ]),

                                Section::make('🏷️ Tags')
                                    ->schema([
                                        TextEntry::make('tags.name')
                                            ->label('')
                                            ->badge()
                                            ->color(fn($record, $state) => $record->tags->where('name', $state)->first()?->color ?? 'gray')
                                            ->separator(',')
                                            ->default('Aucun tag')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsed(),
                            ]),

                        Tabs\Tab::make('Tracking')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Grid::make(2)->schema([
                                    Section::make('🖥️ Device & Navigateur')
                                        ->schema([
                                            TextEntry::make('device_type')
                                                ->label('Type d\'appareil')
                                                ->badge()
                                                ->color(fn($state) => match ($state) {
                                                    'mobile' => 'success',
                                                    'desktop' => 'info',
                                                    'tablet' => 'warning',
                                                    default => 'gray',
                                                })
                                                ->formatStateUsing(fn($state) => match ($state) {
                                                    'mobile' => '📱 Mobile',
                                                    'desktop' => '🖥️ Desktop',
                                                    'tablet' => '📱 Tablet',
                                                    default => 'Inconnu',
                                                }),
                                            TextEntry::make('browser')
                                                ->label('Navigateur')
                                                ->formatStateUsing(fn($state, $record) => $state ? "$state " . ($record->browser_version ?? '') : 'N/A')
                                                ->icon('heroicon-o-globe-alt'),
                                            TextEntry::make('os')
                                                ->label('Système d\'exploitation')
                                                ->formatStateUsing(fn($state, $record) => $state ? "$state " . ($record->os_version ?? '') : 'N/A')
                                                ->icon('heroicon-o-computer-desktop'),
                                            TextEntry::make('screen_resolution')
                                                ->label('Résolution écran')
                                                ->default('N/A')
                                                ->icon('heroicon-o-rectangle-stack'),
                                            TextEntry::make('language')
                                                ->label('Langue')
                                                ->default('N/A')
                                                ->badge(),
                                        ])
                                        ->columnSpan(1),

                                    Section::make('🌍 Géolocalisation')
                                        ->schema([
                                            TextEntry::make('country')
                                                ->label('Pays')
                                                ->formatStateUsing(fn($state) => $state ? (\Locale::getDisplayRegion("-{$state}", 'fr') ?: $state) : 'N/A')
                                                ->icon('heroicon-o-flag')
                                                ->badge()
                                                ->color('primary'),
                                            TextEntry::make('city')
                                                ->label('Ville')
                                                ->default('N/A')
                                                ->icon('heroicon-o-map-pin'),
                                            TextEntry::make('region')
                                                ->label('Région/État')
                                                ->default('N/A'),
                                            TextEntry::make('timezone')
                                                ->label('Fuseau horaire')
                                                ->default('N/A')
                                                ->icon('heroicon-o-clock'),
                                            TextEntry::make('coordinates')
                                                ->label('Coordonnées GPS')
                                                ->state(fn($record) => ($record->latitude && $record->longitude) ? "{$record->latitude}, {$record->longitude}" : 'N/A')
                                                ->copyable(),
                                            TextEntry::make('ip_address')
                                                ->label('Adresse IP')
                                                ->default('N/A')
                                                ->copyable()
                                                ->icon('heroicon-o-server'),
                                        ])
                                        ->columnSpan(1),
                                ]),

                                Section::make('📊 Comportement & Engagement')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('total_time_spent')
                                            ->label('Temps total passé')
                                            ->state(fn($record) => $record->events->sum('time_spent_seconds'))
                                            ->formatStateUsing(fn($state) => $state ? gmdate('H:i:s', $state) : '00:00:00')
                                            ->icon('heroicon-o-clock')
                                            ->color('warning')
                                            ->suffix(' (h:m:s)'),
                                        TextEntry::make('avg_scroll_depth')
                                            ->label('Scroll depth moyen')
                                            ->state(fn($record) => $record->events->avg('scroll_depth_percentage'))
                                            ->formatStateUsing(fn($state) => $state ? round($state) . '%' : 'N/A')
                                            ->icon('heroicon-o-arrow-down')
                                            ->color('info'),
                                        TextEntry::make('events_count')
                                            ->label('Événements totaux')
                                            ->state(fn($record) => $record->events->count())
                                            ->badge()
                                            ->color('success'),
                                        TextEntry::make('page_views')
                                            ->label('Pages vues')
                                            ->state(fn($record) => $record->events()->where('type', 'page_view')->count())
                                            ->icon('heroicon-o-document-text'),
                                        TextEntry::make('form_submits')
                                            ->label('Formulaires soumis')
                                            ->state(fn($record) => $record->events()->where('type', 'form_submit')->count())
                                            ->icon('heroicon-o-document-check'),
                                        TextEntry::make('cta_clicks')
                                            ->label('Clics CTA')
                                            ->state(fn($record) => $record->events()->where('type', 'cta_click')->count())
                                            ->icon('heroicon-o-cursor-arrow-rays'),
                                    ]),

                                Section::make('🎯 Attribution & Source')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('source')
                                            ->label('Source')
                                            ->default('Directe')
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('medium')
                                            ->label('Medium')
                                            ->default('N/A'),
                                        TextEntry::make('campaign')
                                            ->label('Campagne')
                                            ->default('N/A'),
                                        TextEntry::make('referrer')
                                            ->label('Référent')
                                            ->default('N/A')
                                            ->limit(50)
                                            ->tooltip(fn($record) => $record->referrer)
                                            ->copyable(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Activité')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('📊 Résumé d\'engagement')
                                    ->columns(4)
                                    ->schema([
                                        TextEntry::make('total_events')
                                            ->label('Événements totaux')
                                            ->state(fn($record) => $record->events->count())
                                            ->badge()
                                            ->color('info'),
                                        TextEntry::make('total_time')
                                            ->label('Temps total passé')
                                            ->state(fn($record) => $record->events->sum('time_spent_seconds'))
                                            ->formatStateUsing(fn($state) => $state ? gmdate('H:i:s', $state) : '00:00:00')
                                            ->icon('heroicon-o-clock'),
                                        TextEntry::make('avg_scroll')
                                            ->label('Scroll moyen')
                                            ->state(fn($record) => $record->events->avg('scroll_depth_percentage'))
                                            ->formatStateUsing(fn($state) => $state ? round($state) . '%' : 'N/A')
                                            ->icon('heroicon-o-arrow-down'),
                                        TextEntry::make('last_seen')
                                            ->label('Dernière visite')
                                            ->state(fn($record) => $record->last_activity_at)
                                            ->since()
                                            ->color('success'),
                                    ]),

                                Section::make('🕐 Timeline des Événements')
                                    ->description('Historique complet des actions du lead')
                                    ->schema([
                                        TextEntry::make('events_timeline')
                                            ->label('')
                                            ->state(
                                                fn($record) => $record->events()
                                                    ->with('page')
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(50)
                                                    ->get()
                                                    ->map(function ($event) {
                                                        $icon = $event->type->icon();
                                                        $label = $event->type->label();
                                                        $page = $event->page ? $event->page->title : 'Page inconnue';
                                                        $date = $event->created_at->format('d/m/Y H:i');
                                                        $points = $event->type->defaultPoints();

                                                        $details = [];
                                                        if ($event->time_spent_seconds) {
                                                            $details[] = '⏱️ ' . gmdate('i:s', $event->time_spent_seconds);
                                                        }
                                                        if ($event->scroll_depth_percentage) {
                                                            $details[] = '📊 ' . round($event->scroll_depth_percentage) . '%';
                                                        }
                                                        if ($event->device_type) {
                                                            $deviceIcon = match ($event->device_type) {
                                                                'mobile' => '📱',
                                                                'desktop' => '🖥️',
                                                                'tablet' => '📱',
                                                                default => '💻'
                                                            };
                                                            $details[] = $deviceIcon . ' ' . ucfirst($event->device_type);
                                                        }

                                                        $detailsStr = !empty($details) ? ' (' . implode(' • ', $details) . ')' : '';

                                                        return "🔹 **{$label}** sur *{$page}*{$detailsStr}\n   📅 {$date} • +{$points} pts";
                                                    })
                                                    ->join("\n\n")
                                            )
                                            ->markdown()
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsible(),

                                Section::make('📝 Informations système')
                                    ->columns(3)
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Créé le')
                                            ->dateTime('d/m/Y H:i:s')
                                            ->icon('heroicon-o-calendar'),
                                        TextEntry::make('updated_at')
                                            ->label('Mis à jour le')
                                            ->dateTime('d/m/Y H:i:s')
                                            ->icon('heroicon-o-pencil'),
                                        TextEntry::make('converted_at')
                                            ->label('Converti le')
                                            ->dateTime('d/m/Y H:i:s')
                                            ->placeholder('Non converti')
                                            ->icon('heroicon-o-check-badge'),
                                    ])
                                    ->collapsed(),
                            ]),
                    ]),
            ]);
    }
}
