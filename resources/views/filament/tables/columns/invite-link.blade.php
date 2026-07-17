{{--
    Bouton copier avec repli sur document.execCommand('copy') :
    navigator.clipboard échoue silencieusement hors contexte sécurisé (HTTP
    sur un sous-domaine autre que localhost — cas courant en local avec
    *.lvh.me:8000, et potentiellement en prod si un sous-domaine se retrouve
    sans certificat valide). Rendu via ViewColumn (template de confiance) et
    non via formatStateUsing()->html(), qui aurait fait passer ce balisage
    par le sanitizer HTML de Filament et supprimé onclick/x-on/<script>.
--}}
@php($url = $getRecord()->invite_url)
<div
    x-data="{
        copied: false,
        copy() {
            const url = @js($url);
            const fallback = () => {
                const ta = document.createElement('textarea');
                ta.value = url;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(ta);
            };
            const markCopied = () => {
                this.copied = true;
                setTimeout(() => { this.copied = false }, 1500);
            };
            if (window.isSecureContext && navigator.clipboard) {
                navigator.clipboard.writeText(url).then(markCopied, () => { fallback(); markCopied(); });
            } else {
                fallback();
                markCopied();
            }
        },
    }"
    class="inline-flex items-center gap-2"
>
    <span class="fi-ta-text text-sm" title="{{ $url }}">{{ \Illuminate\Support\Str::limit($url, 40) }}</span>
    <button
        type="button"
        x-on:click="copy()"
        class="inline-flex items-center justify-center w-6 h-6 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-500 dark:hover:text-gray-300 dark:hover:bg-white/10 transition-colors"
        :title="copied ? 'Copié !' : 'Copier le lien'"
    >
        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
        <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    </button>
</div>
