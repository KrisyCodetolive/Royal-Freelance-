<?php

namespace App\Services;

use App\Models\EmailSequence;

class SequenceTemplateService
{
    public static function getTemplates(): array
    {
        return [
            'onboarding'       => self::onboarding(),
            'vente'            => self::vente(),
            'relance'          => self::relance(),
            'nurturing'        => self::nurturing(),
            'post_conversion'  => self::postConversion(),
        ];
    }

    public static function getMeta(): array
    {
        return [
            'onboarding' => [
                'label'       => '👋 Onboarding 7 jours',
                'description' => 'Accueil · Guide · Témoignage · Récap (4 emails)',
                'name'        => 'Séquence Onboarding',
                'trigger'     => 'form_submit',
            ],
            'vente' => [
                'label'       => '🎯 Séquence de vente',
                'description' => 'Offre · Bénéfices · Preuve · FAQ · Urgence (5 emails)',
                'name'        => 'Séquence de Vente',
                'trigger'     => 'form_submit',
            ],
            'relance' => [
                'label'       => '🔔 Relance inactifs',
                'description' => 'Absence · Offre spéciale · Dernier message (3 emails)',
                'name'        => 'Relance Inactifs',
                'trigger'     => 'status_changed',
            ],
            'nurturing' => [
                'label'       => '🌱 Nurturing long terme',
                'description' => 'Conseil · Erreur courante · Étude de cas · Upsell (4 emails)',
                'name'        => 'Nurturing Long Terme',
                'trigger'     => 'form_submit',
            ],
            'post_conversion' => [
                'label'       => '🏆 Post-conversion',
                'description' => 'Merci · Guide · Satisfaction · Offre exclusive (4 emails)',
                'name'        => 'Séquence Post-Conversion',
                'trigger'     => 'status_changed',
            ],
        ];
    }

    // Crée la séquence complète (séquence + tous ses emails) depuis un template
    public static function createFromTemplate(string $templateKey, string $name, bool $active = false): ?EmailSequence
    {
        $templates = self::getTemplates();
        $meta      = self::getMeta();

        if (!isset($templates[$templateKey])) {
            return null;
        }

        $sequence = EmailSequence::create([
            'name'    => $name,
            'trigger' => $meta[$templateKey]['trigger'],
            'status'  => $active ? 'active' : 'draft',
            'stats'   => ['sent' => 0, 'opened' => 0, 'clicked' => 0],
        ]);

        foreach ($templates[$templateKey] as $emailData) {
            $sequence->emails()->create([
                'subject'          => $emailData['subject'],
                'content'          => $emailData['content'],
                'send_after_hours' => $emailData['send_after_hours'],
                'is_active'        => true,
                'stats'            => ['sent' => 0, 'opened' => 0, 'clicked' => 0],
            ]);
        }

        return $sequence;
    }

    // Applique un template sur une séquence existante (ajoute ou remplace les emails)
    public static function apply(EmailSequence $sequence, string $templateKey, bool $replace = false): void
    {
        $templates = self::getTemplates();

        if (!isset($templates[$templateKey])) {
            return;
        }

        if ($replace) {
            $sequence->emails()->delete();
        }

        foreach ($templates[$templateKey] as $emailData) {
            $sequence->emails()->create([
                'subject'          => $emailData['subject'],
                'content'          => $emailData['content'],
                'send_after_hours' => $emailData['send_after_hours'],
                'is_active'        => true,
                'stats'            => ['sent' => 0, 'opened' => 0, 'clicked' => 0],
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // Templates
    // -------------------------------------------------------------------------

    private static function onboarding(): array
    {
        return [
            [
                'send_after_hours' => 0,
                'subject'          => 'Bienvenue {first_name} — voici la suite',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Nous sommes ravis de vous accueillir.</p>
<p>Dans les prochains jours, nous allons vous partager tout ce dont vous avez besoin pour bien démarrer.</p>
<p>En attendant, voici ce que vous pouvez faire dès maintenant :</p>
<ul>
  <li>Complétez votre profil</li>
  <li>Explorez notre espace membre</li>
  <li>Rejoignez notre communauté</li>
</ul>
<p>À très vite,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 24,
                'subject'          => '{first_name}, votre guide de démarrage',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Hier vous avez rejoint {funnel_name}. Aujourd\'hui, nous vous offrons un guide complet pour tirer le meilleur de votre expérience.</p>
<p><strong>Les 3 étapes clés :</strong></p>
<ol>
  <li><strong>Étape 1 —</strong> Définissez votre objectif principal</li>
  <li><strong>Étape 2 —</strong> Mettez en place votre premier workflow</li>
  <li><strong>Étape 3 —</strong> Mesurez vos premiers résultats</li>
</ol>
<p>Des questions ? Répondez directement à cet email, nous lisons tout.</p>
<p>Bonne journée,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 72,
                'subject'          => 'Comment {first_name} comme vous ont obtenu des résultats',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Vous n\'êtes pas seul(e) dans cette aventure. Des centaines de personnes comme vous ont déjà franchi ce cap.</p>
<p><em>"Grâce à cette méthode, j\'ai économisé 5 heures par semaine dès le premier mois."</em><br>— Marie L., entrepreneur</p>
<p>La différence entre ceux qui réussissent et les autres ? Ils passent à l\'action rapidement.</p>
<p>Qu\'attendez-vous pour faire pareil ?</p>
<p>À bientôt,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 168,
                'subject'          => '{first_name}, récap de votre première semaine',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Une semaine déjà ! Voici un récapitulatif de ce que nous avons partagé :</p>
<ul>
  <li>✅ Votre guide de démarrage</li>
  <li>✅ Les témoignages de nos membres</li>
  <li>✅ Les étapes clés pour réussir</li>
</ul>
<p>La prochaine étape est la plus importante. Nous allons vous accompagner pour passer au niveau supérieur.</p>
<p>Restez connecté(e),<br>{funnel_name}</p>',
            ],
        ];
    }

    private static function vente(): array
    {
        return [
            [
                'send_after_hours' => 0,
                'subject'          => '{first_name}, découvrez notre offre exclusive',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Merci d\'avoir manifesté votre intérêt. Nous avons quelque chose de spécial pour vous.</p>
<p>Notre offre vous permet de :</p>
<ul>
  <li>Gagner du temps sur vos tâches quotidiennes</li>
  <li>Augmenter vos résultats sans effort supplémentaire</li>
  <li>Bénéficier d\'un accompagnement personnalisé</li>
</ul>
<p>Découvrez tous les détails en cliquant ci-dessous.</p>
<p>Cordialement,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 24,
                'subject'          => 'Les 3 bénéfices que vous ne connaissez pas encore',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Hier je vous présentais notre offre. Aujourd\'hui, je veux vous parler de 3 bénéfices que la plupart des gens découvrent seulement après avoir commencé.</p>
<p><strong>Bénéfice n°1 :</strong> Vous récupérez du temps — en moyenne 3h par semaine.</p>
<p><strong>Bénéfice n°2 :</strong> Vos prospects sont mieux qualifiés, donc plus faciles à convertir.</p>
<p><strong>Bénéfice n°3 :</strong> Tout est automatisé. Vous dormez, le système travaille.</p>
<p>Est-ce que ces résultats vous intéressent, {first_name} ?</p>
<p>À bientôt,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 72,
                'subject'          => 'Ce que nos clients disent (résultats réels)',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Ne me croyez pas sur parole. Voici ce que nos clients disent :</p>
<p><em>"En 30 jours, j\'ai doublé mon nombre de leads qualifiés."</em><br>— Thomas R.</p>
<p><em>"Je n\'aurais jamais pensé que c\'était aussi simple à mettre en place."</em><br>— Sophie M.</p>
<p>Ces résultats ne sont pas des exceptions. Ce sont des exemples de ce que vous pouvez attendre.</p>
<p>Prêt(e) à rejoindre cette liste ?</p>
<p>Bonne journée,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 120,
                'subject'          => '{first_name}, vos questions — nos réponses',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Avant de prendre une décision, vous avez peut-être des questions. Voici les plus fréquentes :</p>
<p><strong>"Est-ce que ça fonctionne pour mon secteur ?"</strong><br>Oui. Notre solution s\'adapte à tout type d\'activité.</p>
<p><strong>"Combien de temps faut-il pour voir des résultats ?"</strong><br>La plupart de nos clients voient les premiers résultats en moins de 2 semaines.</p>
<p><strong>"Et si ça ne me convient pas ?"</strong><br>Nous offrons une garantie satisfait ou remboursé. Zéro risque pour vous.</p>
<p>D\'autres questions ? Répondez directement à cet email.</p>
<p>Cordialement,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 168,
                'subject'          => 'Dernière chance {first_name} — offre valable encore 48h',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Cette semaine, nous avons partagé avec vous tout ce qu\'il y a à savoir sur notre offre.</p>
<p>Je ne veux pas vous mettre la pression, mais cette offre spéciale se termine dans <strong>48 heures</strong>.</p>
<p>Après ça, le tarif normal s\'applique.</p>
<p>Si vous attendiez le bon moment — c\'est maintenant.</p>
<p>À vous de jouer,<br>{funnel_name}</p>',
            ],
        ];
    }

    private static function relance(): array
    {
        return [
            [
                'send_after_hours' => 0,
                'subject'          => '{first_name}, vous nous manquez',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Cela fait un moment que nous n\'avons pas eu de vos nouvelles.</p>
<p>Nous voulions prendre le temps de vous écrire personnellement pour savoir comment vous allez.</p>
<p>Avez-vous des questions sur lesquelles nous pourrions vous aider ? Y a-t-il quelque chose qui vous a freiné(e) ?</p>
<p>Répondez à cet email — nous sommes là pour vous.</p>
<p>À bientôt,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 72,
                'subject'          => 'On a quelque chose spécial pour vous, {first_name}',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Pour vous remercier de votre confiance, nous avons préparé une offre exclusive réservée à nos membres inactifs.</p>
<p>C\'est notre façon de vous dire : <em>on pense à vous</em>.</p>
<p>Cette offre n\'est disponible que pour une poignée de personnes sélectionnées — dont vous.</p>
<p>Profitez-en avant qu\'elle disparaisse.</p>
<p>Bien à vous,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 168,
                'subject'          => '{first_name}, c\'est notre dernier message',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Nous ne voulons pas vous déranger si vous n\'êtes plus intéressé(e).</p>
<p>C\'est pourquoi ceci sera notre dernier email — sauf si vous nous dites que vous souhaitez rester en contact.</p>
<p>Si vous voulez continuer à recevoir nos actualités et offres, cliquez simplement sur le bouton ci-dessous.</p>
<p>Sinon, nous vous souhaitons le meilleur pour la suite.</p>
<p>À bientôt peut-être,<br>{funnel_name}</p>',
            ],
        ];
    }

    private static function nurturing(): array
    {
        return [
            [
                'send_after_hours' => 0,
                'subject'          => 'Un conseil pour bien démarrer, {first_name}',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Avant de vous parler de nos solutions, je voulais vous partager un conseil que j\'aurais aimé recevoir bien plus tôt.</p>
<p><strong>Le conseil :</strong> ne cherchez pas la perfection dès le départ. Commencez avec ce que vous avez, optimisez ensuite.</p>
<p>C\'est le secret des personnes qui avancent vite : elles agissent d\'abord, ajustent ensuite.</p>
<p>Dans les prochaines semaines, je vais partager avec vous d\'autres conseils pratiques comme celui-ci.</p>
<p>Bonne journée,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 168,
                'subject'          => 'L\'erreur que tout le monde fait (et comment l\'éviter)',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Après avoir accompagné des dizaines de professionnels, j\'ai identifié l\'erreur n°1 qui freine presque tout le monde.</p>
<p><strong>L\'erreur :</strong> vouloir tout automatiser d\'un coup.</p>
<p>Le résultat : un système trop complexe, impossible à maintenir, qui finit par être abandonné.</p>
<p><strong>La bonne approche :</strong> automatisez une chose à la fois. Maîtrisez-la. Passez à la suivante.</p>
<p>Simple. Efficace. Durable.</p>
<p>À la semaine prochaine,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 336,
                'subject'          => 'Étude de cas : des résultats concrets en 30 jours',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Cette semaine, je voulais vous partager une histoire vraie.</p>
<p>Marc, consultant indépendant, avait du mal à transformer ses prospects en clients. Il passait des heures à relancer manuellement.</p>
<p><strong>Ce qu\'il a changé :</strong> il a mis en place une séquence email automatisée en 3 étapes.</p>
<p><strong>Résultat après 30 jours :</strong> +40% de taux de conversion, 6h économisées par semaine.</p>
<p>Est-ce que vous aimeriez obtenir le même résultat ?</p>
<p>Répondez à cet email avec "OUI" et je vous dirai comment faire.</p>
<p>Bonne semaine,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 504,
                'subject'          => '{first_name}, prêt(e) à passer à l\'étape suivante ?',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Depuis quelques semaines, je vous partage des conseils pratiques sur la croissance et l\'automatisation.</p>
<p>Maintenant, je voulais vous poser une question directe :</p>
<p><strong>Êtes-vous prêt(e) à aller plus loin ?</strong></p>
<p>Nous avons une solution complète qui regroupe tout ce dont vous avez besoin pour passer au niveau supérieur.</p>
<p>Si vous voulez en savoir plus, répondez à cet email ou cliquez ci-dessous.</p>
<p>À très bientôt,<br>{funnel_name}</p>',
            ],
        ];
    }

    private static function postConversion(): array
    {
        return [
            [
                'send_after_hours' => 0,
                'subject'          => 'Merci {first_name} — voici votre accès',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Merci pour votre confiance. Vous avez fait le bon choix.</p>
<p>Voici ce qui vous attend maintenant :</p>
<ul>
  <li>🔑 Accès immédiat à votre espace personnel</li>
  <li>📚 Tous vos ressources disponibles</li>
  <li>👋 Notre équipe disponible pour vous accompagner</li>
</ul>
<p>Si vous avez des questions, répondez directement à cet email. Nous sommes là.</p>
<p>Bienvenue dans la famille,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 24,
                'subject'          => 'Votre guide complet pour bien démarrer',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Pour vous aider à tirer le maximum de votre investissement, voici votre guide de démarrage complet.</p>
<p><strong>Les 5 premières choses à faire :</strong></p>
<ol>
  <li>Configurez votre profil en 5 minutes</li>
  <li>Explorez le tableau de bord principal</li>
  <li>Lancez votre première action</li>
  <li>Consultez notre base de connaissances</li>
  <li>Rejoignez notre groupe d\'entraide</li>
</ol>
<p>Prenez votre temps. Chaque étape compte.</p>
<p>Bonne installation,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 168,
                'subject'          => '{first_name}, comment se passe votre expérience ?',
                'content'          => '<p>Bonjour {first_name},</p>
<p>Une semaine s\'est écoulée depuis votre démarrage. Nous voulions prendre de vos nouvelles.</p>
<p>Êtes-vous satisfait(e) de votre expérience jusqu\'ici ?</p>
<p>Votre avis est très important pour nous. Il nous aide à améliorer notre service et à mieux vous accompagner.</p>
<p>Répondez à cet email avec vos retours — bons ou mauvais, nous lisons tout et répondons à chaque message.</p>
<p>Merci pour votre confiance,<br>{funnel_name}</p>',
            ],
            [
                'send_after_hours' => 336,
                'subject'          => '{first_name}, une offre exclusive rien que pour vous',
                'content'          => '<p>Bonjour {first_name},</p>
<p>En tant que client fidèle, vous bénéficiez d\'un traitement de faveur.</p>
<p>Nous avons une offre complémentaire qui pourrait vous intéresser — et parce que vous faites déjà partie de notre famille, nous vous la proposons à un tarif préférentiel.</p>
<p>Cette offre n\'est pas publique. Elle est réservée à nos clients existants uniquement.</p>
<p>Vous avez 72h pour en profiter avant qu\'elle disparaisse.</p>
<p>Merci encore pour votre confiance,<br>{funnel_name}</p>',
            ],
        ];
    }
}
