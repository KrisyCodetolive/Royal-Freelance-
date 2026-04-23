@extends('guest.layout')

@section('title', 'Conditions Générales d\'Utilisation')

@section('content')
    <div class="prose prose-slate prose-lg max-w-none prose-headings:font-serif prose-headings:font-medium prose-a:text-amber-600">
        <h1 class="text-4xl mb-8">Conditions Générales d'Utilisation</h1>
        
        <p class="lead text-xl text-slate-500 mb-10">
            Bienvenue sur Royal LeadPro. En utilisant notre service, vous acceptez les présentes conditions qui régissent notre relation.
            <br><span class="text-sm font-normal">Dernière mise à jour : {{ date('d/m/Y') }}</span>
        </p>

        <h3>1. Définitions</h3>
        <ul class="list-disc pl-5">
            <li><strong>Service :</strong> La plateforme SaaS Royal LeadPro accessible via royalleadpro.com.</li>
            <li><strong>Utilisateur :</strong> Toute personne physique ou morale ayant créé un compte.</li>
            <li><strong>Leads :</strong> Les contacts et prospects générés par l'Utilisateur via le Service.</li>
            <li><strong>Tunnels :</strong> Les pages web de marketing créées via le Service.</li>
        </ul>

        <h3>2. Description du Service</h3>
        <p>Royal LeadPro est une solution SaaS permettant aux professionnels de l'immobilier et du commerce de créer des pages de capture (tunnels), de collecter des leads et de gérer leurs prospects via un tableau de bord dédié. Le Service est fourni "tel quel" en mode Software as a Service.</p>

        <h3>3. Compte et Inscription</h3>
        <p>L'accès au Service nécessite la création d'un compte. L'Utilisateur s'engage à fournir des informations exactes. Il est responsable de la confidentialité de ses identifiants. Toute action effectuée depuis son compte est réputée être de son fait.</p>

        <h3>4. Propriété des Données et Leads</h3>
        <p><strong>Vos Leads sont à vous.</strong> Royal LeadPro ne revendique aucun droit de propriété sur les leads générés via vos Tunnels. Vous disposez d'un droit total d'exportation de ces données à tout moment.</p>
        <p>Royal LeadPro s'interdit d'utiliser vos leads à ses propres fins commerciales ou de les revendre à des tiers.</p>

        <h3>5. Obligations de l'Utilisateur (Anti-Spam)</h3>
        <p>L'Utilisateur s'engage à utiliser le Service dans le respect des lois en vigueur (notamment RGPD et CAN-SPAM Act). Il est strictement interdit d'utiliser le Service pour :</p>
        <ul>
            <li>Envoyer des messages non sollicités (SPAM).</li>
            <li>Collecter des données sensibles (santé, orientation politique, etc.) sans autorisation.</li>
            <li>Publier du contenu illégal, diffamatoire ou frauduleux sur les Tunnels.</li>
        </ul>
        <p>Royal LeadPro se réserve le droit de suspendre immédiatement tout compte ne respectant pas ces règles, sans remboursement.</p>

        <h3>6. Conditions Financières</h3>
        <p>L'accès à certaines fonctionnalités avancées nécessite un abonnement payant. Les tarifs sont indiqués sur la page "Tarifs". L'abonnement est facturé mensuellement ou annuellement et est renouvelé par tacite reconduction. L'Utilisateur peut résilier son abonnement à tout moment depuis son espace client (effet à la fin de la période en cours).</p>

        <h3>7. Disponibilité et SLA</h3>
        <p>Nous nous efforçons de maintenir une disponibilité du Service de 99,9%. Toutefois, des interruptions pour maintenance technique peuvent survenir. Nous ne saurions être tenus responsables des pertes de revenus liées à une indisponibilité temporaire du Service.</p>

        <h3>8. Limitation de Responsabilité</h3>
        <p>Royal LeadPro fournit les outils techniques mais ne garantit pas un volume de leads ou un chiffre d'affaires spécifique. L'Utilisateur est seul responsable de sa stratégie marketing et de la conversion de ses prospects.</p>

        <h3>9. Droit applicable</h3>
        <p>Les présentes CGU sont soumises au droit ivoirien. Tout litige relèvera de la compétence exclusive des tribunaux d'Abidjan.</p>
    </div>
@endsection