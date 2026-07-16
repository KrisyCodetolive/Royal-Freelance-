@component('mail::message')
# Vous êtes invité(e) sur {{ $tenantName ?? 'Royal LeadPro' }}

{{ $invitedByName ?? 'Un administrateur' }} vous invite à rejoindre l'espace de travail **{{ $tenantName ?? 'Royal LeadPro' }}** avec le rôle **{{ $roleLabel }}**.

@component('mail::button', ['url' => $inviteUrl])
Rejoindre l'espace de travail
@endcomponent

Ce lien est valable jusqu'au **{{ $expiresAt?->format('d/m/Y à H:i') }}**. Passé ce délai, il faudra demander une nouvelle invitation.

Si vous ne vous attendiez pas à cette invitation, vous pouvez ignorer cet email.

Cordialement,<br>
{{ config('app.name') }}
@endcomponent
