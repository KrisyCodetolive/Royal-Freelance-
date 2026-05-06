<?php

namespace App\Livewire\EmailBuilder;

use App\Models\EmailSequenceEmail;
use Livewire\Component;

class EmailBuilder extends Component
{
    public EmailSequenceEmail $email;

    // Liste des blocs ajoutés à l'email (chaque bloc = tableau avec type + contenu)
    public array $blocks = [];

    // ID du bloc actuellement sélectionné pour édition
    public string $selectedBlockId = '';

    // Afficher le panneau de sélection de template ?
    public bool $showTemplates = true;

    // Types de blocs disponibles pour un email
    public array $blockTypes = [
        ['type' => 'title',   'label' => 'Titre',      'icon' => 'H'],
        ['type' => 'text',    'label' => 'Texte',       'icon' => 'T'],
        ['type' => 'image',   'label' => 'Image',       'icon' => '🖼'],
        ['type' => 'button',  'label' => 'Bouton',      'icon' => '→'],
        ['type' => 'divider', 'label' => 'Séparateur',  'icon' => '—'],
        ['type' => 'spacer',  'label' => 'Espace',      'icon' => '↕'],
    ];

    public function mount(EmailSequenceEmail $email): void
    {
        $this->email = $email;

        // Si l'email a déjà des blocs sauvegardés, les charger
        $savedBlocks = $email->settings['builder_blocks'] ?? null;
        if ($savedBlocks) {
            $this->blocks = $savedBlocks;
            $this->showTemplates = false;
        }
    }

    // Choisir un template → pré-remplit les blocs
    public function selectTemplate(string $template): void
    {
        $this->blocks = $this->getTemplate($template);
        $this->showTemplates = false;
    }

    // Partir de zéro sans template
    public function startBlank(): void
    {
        $this->blocks = [];
        $this->showTemplates = false;
    }

    // Ajouter un bloc à la fin
    public function addBlock(string $type): void
    {
        $this->blocks[] = [
            'id'      => uniqid(),
            'type'    => $type,
            'content' => $this->getDefaultContent($type),
        ];
    }

    // Sélectionner un bloc pour l'éditer
    public function selectBlock(string $blockId): void
    {
        $this->selectedBlockId = $blockId;
    }

    // Désélectionner
    public function deselectBlock(): void
    {
        $this->selectedBlockId = '';
    }

    // Mettre à jour le contenu d'un bloc
    public function updateBlock(string $blockId, array $content): void
    {
        foreach ($this->blocks as $index => $block) {
            if ($block['id'] === $blockId) {
                $this->blocks[$index]['content'] = array_merge($block['content'], $content);
                break;
            }
        }
    }

    // Supprimer un bloc
    public function deleteBlock(string $blockId): void
    {
        $this->blocks = array_values(
            array_filter($this->blocks, fn($b) => $b['id'] !== $blockId)
        );
        $this->selectedBlockId = '';
    }

    // Monter un bloc
    public function moveUp(string $blockId): void
    {
        foreach ($this->blocks as $index => $block) {
            if ($block['id'] === $blockId && $index > 0) {
                [$this->blocks[$index - 1], $this->blocks[$index]] =
                    [$this->blocks[$index], $this->blocks[$index - 1]];
                break;
            }
        }
    }

    // Descendre un bloc
    public function moveDown(string $blockId): void
    {
        $last = count($this->blocks) - 1;
        foreach ($this->blocks as $index => $block) {
            if ($block['id'] === $blockId && $index < $last) {
                [$this->blocks[$index + 1], $this->blocks[$index]] =
                    [$this->blocks[$index], $this->blocks[$index + 1]];
                break;
            }
        }
    }

    // Sauvegarder → génère le HTML et sauvegarde dans l'email
    public function save(): void
    {
        $html = $this->renderToHtml();

        $settings = $this->email->settings ?? [];
        $settings['builder_blocks'] = $this->blocks;

        $this->email->update([
            'content'  => $html,
            'settings' => $settings,
        ]);

        session()->flash('saved', 'Email sauvegardé.');
    }

    // Génère le HTML final à partir des blocs
    private function renderToHtml(): string
    {
        $html = '';
        foreach ($this->blocks as $block) {
            $html .= view('livewire.email-builder.blocks.' . $block['type'], [
                'content' => $block['content'],
                'render'  => true,
            ])->render();
        }

        return view('emails.layouts.sequence', [
            'subject'        => $this->email->subject,
            'content'        => $html,
            'tenant_name'    => $this->email->emailSequence?->tenant?->name ?? '',
            'unsubscribeUrl' => '#',
            'pixelUrl'       => '',
        ])->render();
    }

    // Contenu par défaut pour chaque type de bloc
    private function getDefaultContent(string $type): array
    {
        return match ($type) {
            'title'   => ['text' => 'Votre titre ici', 'level' => 'h2', 'align' => 'center', 'color' => '#1a1a1a'],
            'text'    => ['text' => 'Écrivez votre texte ici...', 'align' => 'left', 'color' => '#333333'],
            'image'   => ['url' => '', 'alt' => '', 'align' => 'center', 'width' => '100%'],
            'button'  => ['text' => 'Cliquez ici', 'url' => '#', 'align' => 'center', 'bg_color' => '#6366f1', 'text_color' => '#ffffff'],
            'divider' => ['color' => '#e5e7eb', 'margin' => '20px'],
            'spacer'  => ['height' => '30px'],
            default   => [],
        };
    }

    // Templates pré-définis
    private function getTemplate(string $template): array
    {
        return match ($template) {
            'welcome' => [
                ['id' => uniqid(), 'type' => 'image',   'content' => ['url' => '', 'alt' => 'Logo', 'align' => 'center', 'width' => '150px']],
                ['id' => uniqid(), 'type' => 'spacer',  'content' => ['height' => '20px']],
                ['id' => uniqid(), 'type' => 'title',   'content' => ['text' => 'Bienvenue {first_name} !', 'level' => 'h1', 'align' => 'center', 'color' => '#1a1a1a']],
                ['id' => uniqid(), 'type' => 'text',    'content' => ['text' => 'Merci de nous rejoindre. Nous sommes ravis de vous accueillir.', 'align' => 'center', 'color' => '#555555']],
                ['id' => uniqid(), 'type' => 'spacer',  'content' => ['height' => '20px']],
                ['id' => uniqid(), 'type' => 'button',  'content' => ['text' => 'Découvrir maintenant', 'url' => '#', 'align' => 'center', 'bg_color' => '#6366f1', 'text_color' => '#ffffff']],
                ['id' => uniqid(), 'type' => 'divider', 'content' => ['color' => '#e5e7eb', 'margin' => '30px']],
            ],
            'offer' => [
                ['id' => uniqid(), 'type' => 'title',   'content' => ['text' => '🎯 Offre exclusive pour {first_name}', 'level' => 'h1', 'align' => 'center', 'color' => '#1a1a1a']],
                ['id' => uniqid(), 'type' => 'text',    'content' => ['text' => 'Vous avez montré un vrai engagement. Voici une offre spéciale réservée pour vous.', 'align' => 'center', 'color' => '#555555']],
                ['id' => uniqid(), 'type' => 'spacer',  'content' => ['height' => '20px']],
                ['id' => uniqid(), 'type' => 'button',  'content' => ['text' => '🔥 Profiter de l\'offre', 'url' => '#', 'align' => 'center', 'bg_color' => '#ef4444', 'text_color' => '#ffffff']],
                ['id' => uniqid(), 'type' => 'spacer',  'content' => ['height' => '10px']],
                ['id' => uniqid(), 'type' => 'text',    'content' => ['text' => 'Offre valable 48h seulement.', 'align' => 'center', 'color' => '#999999']],
            ],
            'followup' => [
                ['id' => uniqid(), 'type' => 'title',   'content' => ['text' => '{first_name}, on prend de vos nouvelles', 'level' => 'h2', 'align' => 'left', 'color' => '#1a1a1a']],
                ['id' => uniqid(), 'type' => 'text',    'content' => ['text' => 'Bonjour {first_name},\n\nNous n\'avons pas eu de nouvelles récemment. Avez-vous des questions ?', 'align' => 'left', 'color' => '#333333']],
                ['id' => uniqid(), 'type' => 'spacer',  'content' => ['height' => '20px']],
                ['id' => uniqid(), 'type' => 'button',  'content' => ['text' => 'Répondre', 'url' => '#', 'align' => 'left', 'bg_color' => '#10b981', 'text_color' => '#ffffff']],
            ],
            'relance' => [
                ['id' => uniqid(), 'type' => 'title',   'content' => ['text' => '{first_name}, il reste peu de temps !', 'level' => 'h1', 'align' => 'center', 'color' => '#1a1a1a']],
                ['id' => uniqid(), 'type' => 'text',    'content' => ['text' => 'Vous avez commencé quelque chose d\'important. Ne laissez pas cette opportunité passer.', 'align' => 'center', 'color' => '#555555']],
                ['id' => uniqid(), 'type' => 'spacer',  'content' => ['height' => '20px']],
                ['id' => uniqid(), 'type' => 'button',  'content' => ['text' => '⏰ Je reprends maintenant', 'url' => '#', 'align' => 'center', 'bg_color' => '#f97316', 'text_color' => '#ffffff']],
                ['id' => uniqid(), 'type' => 'spacer',  'content' => ['height' => '10px']],
                ['id' => uniqid(), 'type' => 'button',  'content' => ['text' => 'Non merci', 'url' => '#', 'align' => 'center', 'bg_color' => '#6b7280', 'text_color' => '#ffffff']],
            ],
            default => [],
        };
    }

    public function render()
    {
        return view('livewire.email-builder.email-builder')
            ->layout('layouts.email-builder');
    }
}
