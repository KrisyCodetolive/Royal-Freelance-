<?php

namespace App\Filament\Resources\Funnels\Pages;

use App\Filament\Concerns\EnforcesTenantQuota;
use App\Filament\Resources\Funnels\FunnelResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateFunnel extends CreateRecord
{
    use EnforcesTenantQuota;

    protected static string $resource = FunnelResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $this->haltIfQuotaReached('tunnels', 'tunnels');

        return parent::handleRecordCreation($data);
    }
}
