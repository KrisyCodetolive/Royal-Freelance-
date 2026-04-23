<?php

namespace App\Filament\Resources\Funnels\Pages;

use App\Enums\PageType;
use App\Filament\Resources\Funnels\FunnelResource;
use App\Models\Funnel;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page as FilamentPage;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ManageFunnelPages extends FilamentPage implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = FunnelResource::class;

    protected string $view = 'filament.resources.funnels.pages.manage-funnel-pages';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string
    {
        return 'Pages de : ' . $this->record->name;
    }

    public static function getNavigationLabel(): string
    {
        return 'Gérer les Pages';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_page')
                ->label('Ajouter une Page')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    TextInput::make('title')
                        ->label('Titre de la page')
                        ->required()
                        ->maxLength(255),
                    Select::make('type')
                        ->label('Type de page')
                        ->options(PageType::class)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $maxOrder = $this->record->pages()->max('sort_order') ?? 0;

                    Page::create([
                        'funnel_id' => $this->record->id,
                        'title' => $data['title'],
                        'slug' => Str::slug($data['title']),
                        'type' => $data['type'],
                        'sort_order' => $maxOrder + 1,
                        'is_active' => true,
                        'is_required' => true,
                    ]);

                    Notification::make()
                        ->title('Page créée')
                        ->success()
                        ->send();
                }),
            Action::make('back')
                ->label('Retour au tunnel')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn() => FunnelResource::getUrl('edit', ['record' => $this->record])),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->record->pages()->ordered()->getQuery())
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->label() ?? $state),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                TextColumn::make('blocks_count')
                    ->label('Blocs')
                    ->counts('blocks')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('views_count')
                    ->label('Vues')
                    ->numeric(),
                TextColumn::make('submissions_count')
                    ->label('Soumissions')
                    ->numeric(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordAction('edit_page')
            ->actions([
                Action::make('edit_page')
                    ->label('Modifier')
                    ->icon('heroicon-o-pencil')
                    ->url(fn(Page $record) => route('page-builder.edit', ['model' => 'Page', 'modelId' => $record->id])),
                Action::make('move_up')
                    ->label('Monter')
                    ->icon('heroicon-o-chevron-up')
                    ->action(function (Page $record) {
                        $previous = $this->record->pages()
                            ->where('sort_order', '<', $record->sort_order)
                            ->orderByDesc('sort_order')
                            ->first();

                        if ($previous) {
                            $temp = $record->sort_order;
                            $record->update(['sort_order' => $previous->sort_order]);
                            $previous->update(['sort_order' => $temp]);
                        }
                    }),
                Action::make('move_down')
                    ->label('Descendre')
                    ->icon('heroicon-o-chevron-down')
                    ->action(function (Page $record) {
                        $next = $this->record->pages()
                            ->where('sort_order', '>', $record->sort_order)
                            ->orderBy('sort_order')
                            ->first();

                        if ($next) {
                            $temp = $record->sort_order;
                            $record->update(['sort_order' => $next->sort_order]);
                            $next->update(['sort_order' => $temp]);
                        }
                    }),
                Action::make('toggle_active')
                    ->label(fn(Page $record) => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn(Page $record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color('gray')
                    ->action(fn(Page $record) => $record->update(['is_active' => !$record->is_active])),
                Action::make('preview')
                    ->label('Voir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn(Page $record) => $record->getPublicUrl())
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ]);
    }
}
