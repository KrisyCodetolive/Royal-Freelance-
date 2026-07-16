<?php

namespace App\Mail;

use App\Models\TenantInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TenantInvitation $invitation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation à rejoindre ' . ($this->invitation->tenant?->name ?? 'Royal LeadPro'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenant-invitation',
            with: [
                'inviteUrl' => $this->invitation->invite_url,
                'tenantName' => $this->invitation->tenant?->name,
                'invitedByName' => $this->invitation->invitedBy?->name,
                'roleLabel' => match ($this->invitation->role) {
                    'admin' => 'Administrateur',
                    'editor' => 'Editor',
                    'viewer' => 'Viewer',
                    'commercial' => 'Commercial',
                    default => $this->invitation->role,
                },
                'expiresAt' => $this->invitation->expires_at,
            ],
        );
    }
}
