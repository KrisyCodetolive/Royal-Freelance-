<?php

namespace App\Filament\Resources\Funnels\RelationManagers;

use App\Enums\EventType;
use App\Models\Event;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\Alignment;
use Filament\Actions; // Using unified Actions namespace


class AnalyticsRelationManager extends RelationManager
{
    protected static string $relationship = 'analytics';

    protected static ?string $title = '📊 Analytics & Tracking';

    protected static ?string $modelLabel = 'Événement';

    protected static ?string $pluralModelLabel = 'Événements';

    protected static ?string $recordTitleAttribute = 'type';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Type d\'événement')
                    ->options(EventType::class)
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('lead_id')
                    ->label('Lead')
                    ->relationship('lead', 'email')
                    ->searchable()
                    ->disabled(),

                Forms\Components\Select::make('page_id')
                    ->label('Page')
                    ->relationship('page', 'title')
                    ->searchable()
                    ->disabled(),

                Forms\Components\KeyValue::make('data')
                    ->label('Données')
                    ->disabled(),

                Forms\Components\TextInput::make('ip_address')
                    ->label('Adresse IP')
                    ->disabled(),

                Forms\Components\Textarea::make('user_agent')
                    ->label('User Agent')
                    ->disabled()
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Événement')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => match($state) {
                        EventType::PAGE_VIEW => 'gray',
                        EventType::FORM_SUBMIT => 'success',
                        EventType::VIDEO_PLAY, EventType::VIDEO_25, EventType::VIDEO_50, EventType::VIDEO_75, EventType::VIDEO_100 => 'info',
                        EventType::CTA_CLICK => 'warning',
                        EventType::WHATSAPP_CLICK => 'success',
                        EventType::PAYMENT_CLICK => 'danger',
                        EventType::CONVERSION => 'success',
                        default => 'gray',
                    })
                    ->icon(fn($state) => $state->icon())
                    ->sortable(),

                Tables\Columns\TextColumn::make('lead.email')
                    ->label('Lead')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => $record->lead ? route('filament.admin.resources.leads.view', $record->lead) : null)
                    ->color('primary')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('page.title')
                    ->label('Page')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn($record) => $record->page?->title),

                Tables\Columns\TextColumn::make('data')
                    ->label('Détails')
                    ->formatStateUsing(function ($record) {
                        $data = $record->data ?? [];
                        $details = [];
                        
                        if (isset($data['percentage'])) {
                            $details[] = "Progression: {$data['percentage']}%";
                        }
                        if (isset($data['video_id'])) {
                            $details[] = "Vidéo: {$data['video_id']}";
                        }
                        if (isset($data['button_id'])) {
                            $details[] = "Bouton: {$data['button_id']}";
                        }
                        if (isset($data['form_data']) && is_array($data['form_data'])) {
                            $details[] = count($data['form_data']) . " champs";
                        }
                        
                        return implode(' • ', $details) ?: '-';
                    })
                    ->limit(50)
                    ->tooltip(fn($record) => json_encode($record->data, JSON_PRETTY_PRINT)),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->alignment(Alignment::Center),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type d\'événement')
                    ->options(EventType::class)
                    ->multiple(),

                Tables\Filters\Filter::make('video_events')
                    ->label('Événements vidéo')
                    ->query(fn (Builder $query): Builder => $query->whereIn('type', [
                        EventType::VIDEO_PLAY,
                        EventType::VIDEO_25,
                        EventType::VIDEO_50,
                        EventType::VIDEO_75,
                        EventType::VIDEO_100,
                    ]))
                    ->toggle(),

                Tables\Filters\Filter::make('conversion_events')
                    ->label('Événements de conversion')
                    ->query(fn (Builder $query): Builder => $query->whereIn('type', [
                        EventType::WHATSAPP_CLICK,
                        EventType::PAYMENT_CLICK,
                        EventType::CONVERSION,
                    ]))
                    ->toggle(),

                Tables\Filters\Filter::make('today')
                    ->label('Aujourd\'hui')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today()))
                    ->toggle(),

                Tables\Filters\Filter::make('this_week')
                    ->label('Cette semaine')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ]))
                    ->toggle(),
            ])
            ->headerActions([
                Actions\Action::make('export_analytics')
                    ->label('📈 Rapport Analytics')
                    ->icon('heroicon-o-chart-bar')
                    ->color('success')
                    ->action(function () {
                        // TODO: Implement analytics export
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aucun événement')
            ->emptyStateDescription('Les événements de tracking apparaîtront ici dès que les visiteurs interagiront avec le tunnel.')
            ->emptyStateIcon('heroicon-o-chart-bar');
    }

    protected function getTableQuery(): Builder
    {
        // Get events from all pages of this funnel
        $funnel = $this->getOwnerRecord();
        
        return Event::query()
            ->whereHas('page', function (Builder $query) use ($funnel) {
                $query->where('funnel_id', $funnel->id);
            })
            ->with(['lead', 'page']);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
