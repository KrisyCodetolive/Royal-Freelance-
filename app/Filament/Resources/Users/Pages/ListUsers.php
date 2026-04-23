<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_demo_commercials')
                ->label('Générer commerciaux démo')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->form([
                    TextInput::make('count')
                        ->label('Nombre de commerciaux')
                        ->numeric()
                        ->default(5)
                        ->minValue(1)
                        ->maxValue(20)
                        ->required(),
                ])
                ->requiresConfirmation()
                ->modalHeading('Générer des commerciaux de démonstration')
                ->modalDescription('Cette action va créer des commerciaux fictifs pour tester le système.')
                ->action(function (array $data) {
                    $count = (int) $data['count'];
                    $tenantId = auth()->user()->tenant_id;

                    $firstNames = ['Jean', 'Marie', 'Pierre', 'Sophie', 'Paul', 'Émilie', 'Lucas', 'Camille', 'Thomas', 'Julie', 'Nicolas', 'Laura', 'Antoine', 'Léa', 'Maxime', 'Chloé', 'Alexandre', 'Emma', 'Julien', 'Sarah'];
                    $lastNames = ['Dupont', 'Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand', 'Leroy', 'Moreau', 'Simon', 'Laurent', 'Lefebvre', 'Michel'];
                    $shopNames = ['Ma Boutique', 'Shop Express', 'Store Premium', 'Vente Flash', 'Promo Plus', 'Discount Pro', 'Super Deal', 'Méga Store', 'Top Ventes', 'Best Shop'];

                    $created = 0;
                    for ($i = 0; $i < $count; $i++) {
                        $firstName = $firstNames[array_rand($firstNames)];
                        $lastName = $lastNames[array_rand($lastNames)];
                        $name = $firstName . ' ' . $lastName;
                        $email = Str::slug($firstName) . '.' . Str::slug($lastName) . rand(1, 999) . '@demo.com';

                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'password' => bcrypt('password'),
                            'tenant_id' => $tenantId,
                            'shop_name' => $shopNames[array_rand($shopNames)] . ' ' . $firstName,
                            'whatsapp_number' => '+237' . rand(600000000, 699999999),
                            'bio' => "Commercial passionné avec une expertise en vente en ligne. Spécialisé dans le MLM et les produits digitaux.",
                            'is_active' => true,
                        ]);

                        $user->assignRole('commercial');
                        $created++;
                    }

                    Notification::make()
                        ->title('Commerciaux créés')
                        ->body("{$created} commercial(aux) de démonstration ont été créés avec succès. Mot de passe: 'password'")
                        ->success()
                        ->duration(10000)
                        ->send();
                }),

            CreateAction::make()
                ->label('Nouveau commercial'),
        ];
    }
}
