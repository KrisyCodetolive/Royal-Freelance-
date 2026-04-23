<?php

namespace App\Filament\Resources\Funnels\RelationManagers;

use App\Enums\PageType;
use App\Models\Page;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions; // Using unified Actions namespace
use Illuminate\Support\Str;

class PagesRelationManager extends RelationManager
{
    protected static string $relationship = 'pages';

    protected static ?string $title = 'Pages de ce tunnel';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                Forms\Components\Hidden::make('slug'),

                Forms\Components\Select::make('type')
                    ->label('Type de page')
                    ->options(PageType::class)
                    ->required(),

                Forms\Components\Hidden::make('sort_order')
                    ->default(fn(RelationManager $livewire) => $livewire->getOwnerRecord()->pages()->max('sort_order') + 1),

                Forms\Components\Hidden::make('is_active')
                    ->default(true),

                Forms\Components\Hidden::make('is_required')
                    ->default(true),

                Forms\Components\Select::make('page_template')
                    ->label('Modèle de contenu (Optionnel)')
                    ->options(\App\Services\TemplateGeneratorService::getPageTemplates())
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->helperText('Sélectionnez un modèle pour pré-remplir la page avec des blocs.')
                    ->hiddenOn('edit'), // Ne pas afficher lors de l'édition
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state instanceof PageType ? $state->label() : $state),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('blocks_count')
                    ->label('Blocs')
                    ->counts('blocks')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Vues')
                    ->numeric(),
                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Leads')
                    ->numeric(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Ajouter une Page')
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $data['funnel_id'] = $livewire->getOwnerRecord()->id;
                        if (!isset($data['slug'])) {
                            $data['slug'] = Str::slug($data['title']);
                        }
                        if (!isset($data['sort_order'])) {
                            $data['sort_order'] = $livewire->getOwnerRecord()->pages()->max('sort_order') + 1;
                        }
                        return $data;
                    })
                    ->using(function (array $data, string $model): Page {
                        $templateKey = $data['page_template'] ?? null;
                        unset($data['page_template']);

                        $page = $model::create($data);

                        if ($templateKey) {
                            app(\App\Services\TemplateGeneratorService::class)->applyPageTemplate($page, $templateKey);
                        }

                        return $page;
                    }),
            ])
            ->actions([
                Actions\Action::make('edit_builder')
                    ->label('Ouvrir Builder')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->url(fn(Page $record) => route('page-builder.edit', ['model' => 'Page', 'modelId' => $record->id]))
                    ->openUrlInNewTab(),

                Actions\EditAction::make(),

                Actions\Action::make('duplicate')
                    ->label('Dupliquer')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->action(fn(Page $record) => $record->duplicate())
                    ->requiresConfirmation()
                    ->successNotificationTitle('Page dupliquée avec succès'),

                Actions\DeleteAction::make(),

                Actions\Action::make('preview')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Page $record) => $record->getPublicUrl())
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
