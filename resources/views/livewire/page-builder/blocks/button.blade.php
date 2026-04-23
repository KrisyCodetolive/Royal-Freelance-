@php
    use App\Helpers\BlockStyleHelper;

    $text = $content['text'] ?? 'Cliquer ici';
    $actionType = $content['action_type'] ?? 'url';
    $target = $content['target'] ?? '_self';
    $icon = $content['icon'] ?? null;
    $size = $content['size'] ?? 'medium';
    $fullWidth = $content['full_width'] ?? false;

    // Récupérer la page liée si page_id est défini
    $linkedPage = null;
    if ($actionType === 'page' && ($content['page_id'] ?? false)) {
        $linkedPage = \App\Models\Page::find($content['page_id']);
    }

    // Utiliser les valeurs du tunnel parent si non définies au niveau du bloc
    $funnel = $block->page->funnel;

    // Prioriser le WhatsApp du commercial si disponible (pour le mode preview, ces variables peuvent ne pas exister)
    $defaultWhatsappNumber = (!empty($commercialWhatsApp) ? $commercialWhatsApp : null) ?? $funnel->whatsapp_url ?? '';
    $defaultWhatsappMessage = (!empty($commercialWhatsAppMessage) ? $commercialWhatsAppMessage : null) ?? $funnel->whatsapp_message ?? '';

    // Prioriser le WhatsApp du commercial s'il est présent
    // Sinon utiliser celui saisi dans le bloc s'il existe
    // Sinon utiliser le défaut du tunnel
    $whatsappNumber = (!empty($commercialWhatsApp) ? $commercialWhatsApp : null)
        ?? (!empty($content['whatsapp_number']) ? $content['whatsapp_number'] : $funnel->whatsapp_url)
        ?? '';

    $whatsappMessage = (!empty($commercialWhatsAppMessage) ? $commercialWhatsAppMessage : null)
        ?? (!empty($content['whatsapp_message']) ? $content['whatsapp_message'] : $funnel->whatsapp_message)
        ?? '';

    $shopUrl = (!empty($content['shop_url']) ? $content['shop_url'] : $funnel->payment_url) ?? '#';

    // Generate URL based on action type
    $url = match ($actionType) {
        'page' => $linkedPage ? route('funnel.page', [
            'funnelSlug' => $linkedPage->funnel->slug,
            'pageSlug' => $linkedPage->slug
        ]) : '#',
        'whatsapp' => 'https://wa.me/' . ltrim($whatsappNumber, '+') . '?text=' . urlencode($whatsappMessage),
        'whatsapp_group' => $content['whatsapp_group_url'] ?? '#',
        'shop' => $shopUrl,
        default => $content['url'] ?? '#',
    };

    // Size presets
    $sizeStyles = match ($size) {
        'small' => ['padding' => '8px 20px', 'fontSize' => '0.875rem'],
        'large' => ['padding' => '16px 48px', 'fontSize' => '1.125rem'],
        default => ['padding' => '12px 32px', 'fontSize' => '1rem'],
    };

    $defaultStyles = array_merge($sizeStyles, [
        'backgroundColor' => $branding['primary_color'] ?? '#3B82F6',
        'color' => '#FFFFFF',
        'borderRadius' => '8px',
        'fontWeight' => '600',
        'textAlign' => 'center',
        'display' => 'inline-flex',
        'alignItems' => 'center',
        'justifyContent' => 'center',
        'gap' => '8px',
        'transition' => 'all 0.3s ease',
        'textDecoration' => 'none',
        'cursor' => 'pointer',
        'border' => 'none',
    ]);

    $buttonStyle = BlockStyleHelper::generateStyle($styles, $defaultStyles);
    $animation = BlockStyleHelper::getAnimationAttribute($styles);

    // Container styles
    $containerStyle = collect([
        'text-align' => $styles['textAlign'] ?? 'center',
        'margin' => $styles['margin'] ?? '0 auto',
        'max-width' => $styles['maxWidth'] ?? null,
    ])->filter()->map(fn($v, $k) => "$k: $v")->implode('; ');
@endphp

<div style="{{ $containerStyle }}">
    <a href="{{ $url }}" target="{{ $target }}" class="cta-button {{ $fullWidth ? 'w-full' : '' }}"
        style="{{ $buttonStyle }}{{ $fullWidth ? '; width: 100%;' : '' }}" {!! $animation !!}
        onmouseover="this.style.opacity='0.9'; this.style.transform='translateY(-2px)'"
        onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)'">

        @if($icon === 'whatsapp')
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
            </svg>
        @elseif($icon === 'arrow')
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        @elseif($icon === 'check')
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        @elseif($icon === 'shopping-cart')
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        @elseif($icon === 'users')
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        @endif

        <span>{{ $text }}</span>

        @if($icon === 'arrow-right')
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
        @endif
    </a>
</div>