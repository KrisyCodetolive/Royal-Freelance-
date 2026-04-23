<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\CommercialGroup;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_commercial_dashboard')
                ->label('Se connecter en tant que')
                ->icon('heroicon-o-arrow-right-end-on-rectangle')
                ->color('warning')
                ->visible(fn() => $this->record->hasRole('commercial'))
                ->requiresConfirmation()
                ->modalHeading('Accéder au compte commercial')
                ->modalDescription('Vous allez être déconnecté de votre compte administrateur pour accéder au compte de ce commercial. Vous devrez vous reconnecter pour revenir à l\'administration.')
                ->action(function () {
                    \Illuminate\Support\Facades\Auth::login($this->record);
                    return redirect()->route('commercial.dashboard');
                }),

            EditAction::make(),
        ];
    }
}
