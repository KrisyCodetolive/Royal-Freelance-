<?php

namespace App\Events;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Lead $lead,
        public LeadStatus $oldStatus,
        public LeadStatus $newStatus
    ) {
    }

    public function isNowHot(): bool
    {
        return in_array($this->newStatus, [LeadStatus::HOT, LeadStatus::ULTRA_HOT])
            && !in_array($this->oldStatus, [LeadStatus::HOT, LeadStatus::ULTRA_HOT]);
    }

    public function isNowClient(): bool
    {
        return $this->newStatus === LeadStatus::CLIENT
            && $this->oldStatus !== LeadStatus::CLIENT;
    }
}
