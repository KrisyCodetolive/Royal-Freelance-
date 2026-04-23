<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Template extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'subject',
        'content',
        'variables',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEmail($query)
    {
        return $query->where('type', 'email');
    }

    public function scopeWhatsApp($query)
    {
        return $query->where('type', 'whatsapp');
    }

    public function scopeSms($query)
    {
        return $query->where('type', 'sms');
    }

    // Helpers
    public function render(array $data = []): string
    {
        $content = $this->content;

        foreach ($data as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }

        return $content;
    }

    public function renderSubject(array $data = []): string
    {
        if (!$this->subject) {
            return '';
        }

        $subject = $this->subject;

        foreach ($data as $key => $value) {
            $subject = str_replace("{{$key}}", $value, $subject);
        }

        return $subject;
    }

    public function getAvailableVariables(): array
    {
        return $this->variables ?? [
            'lead.first_name',
            'lead.last_name',
            'lead.email',
            'lead.phone',
            'funnel.name',
            'offer.name',
            'offer.price',
        ];
    }
}
