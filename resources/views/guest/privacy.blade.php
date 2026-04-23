@extends('guest.layout')

@section('title', 'Politique de Confidentialité')

@section('content')
    <div
        class="prose prose-slate prose-lg max-w-none prose-headings:font-serif prose-headings:font-medium prose-a:text-amber-600">
        <h1 class="text-4xl mb-8">Politique de Confidentialité</h1>

        <p class="lead text-xl text-slate-500 mb-10">
            Chez Royal LeadPro, la confiance est le fondement de notre relation. Nous nous engageons à protéger vos données
            personnelles et celles de vos leads avec la plus grande rigueur.
        </p>

        <div class="p-4 bg-slate-50 rounded-lg border-l-4 border-amber-500 mb-8 not-prose">
            <p class="text-slate-700 font-medium">Responsable du traitement :</p>
            <p class="text-slate-600 text-sm">Royal LeadPro<br>Abidjan, Côte d'Ivoire<br>Email : <a
                    href="mailto:privacy@royalleadpro.com"
                    class="text-amber-600 hover:underline">privacy@royalleadpro.com</a></p>
        </div>

        <h3>1. Données collectées</h3>
        <p>Dans le cadre de l'utilisation de notre plateforme SaaS, nous collectons deux types de données :</p>

        <h4 class="text-lg font-medium text-slate-900 mt-4 mb-2">A. Données de votre compte (Utilisateur)</h4>
        <ul class="list-disc pl-5 space-y-1">
            <li><strong>Identité :</strong> Nom, prénom, adresse email professionnelle, numéro de téléphone.</li>
            <li><strong>Professionnel :</strong> Nom de l'entreprise, secteur d'activité, URL du site web.</li>
            <li><strong>Facturation :</strong> Historique de paiements, adresse de facturation (les données bancaires sont
                gérées de manière sécurisée par notre prestataire de paiement).</li>
            <li><strong>Connexion :</strong> Logs de connexion, adresse IP, type de navigateur.</li>
        </ul>

        <h4 class="text-lg font-medium text-slate-900 mt-4 mb-2">B. Données de vos Leads (Clients finaux)</h4>
        <p>En tant que sous-traitant technique, nous hébergeons les données que vous collectez via nos outils (Tunnels,
            Formulaires) :</p>
        <ul class="list-disc pl-5 space-y-1">
            <li>Informations de contact (Email, Téléphone).</li>
            <li>Réponses aux questionnaires de qualification.</li>
            <li>Données de navigation sur vos pages de vente.</li>
        </ul>
        <p class="text-sm italic text-slate-500 mt-2">Note : Vous restez le Responsable de Traitement pour les données de
            vos propres leads. Royal LeadPro agit en tant que processeur de données.</p>

        <h3>2. Utilisation des données</h3>
        <p>Nous traitons les données pour les finalités suivantes :</p>
        <ul>
            <li><strong>Fourniture du Service :</strong> Création de compte, accès au dashboard, génération de tunnels,
                hébergement des leads.</li>
            <li><strong>Amélioration du Produit :</strong> Analyse des performances des tunnels (anonymisée) pour optimiser
                nos algorithmes de conversion.</li>
            <li><strong>Support Client :</strong> Réponse à vos tickets et assistance technique.</li>
            <li><strong>Sécurité :</strong> Détection et prévention des fraudes ou abus (spam).</li>
            <li><strong>Communication :</strong> Envoi de newsletters produit et conseils marketing (avec votre
                consentement).</li>
        </ul>

        <h3>3. Partage et Sous-traitance</h3>
        <p>Vos données sont strictement confidentielles. Elles ne sont transmises qu'aux tiers nécessaires au bon
            fonctionnement du service :</p>
        <ul>
            <li><strong>Hébergement :</strong> Serveurs sécurisés (données chiffrées).</li>
            <li><strong>Paiement :</strong> Processeurs de paiement certifiés PCI-DSS.</li>
            <li><strong>Emailing :</strong> Services d'envoi d'emails transactionnels.</li>
        </ul>
        <p>Nous ne vendons <strong>jamais</strong> vos données ni celles de vos leads à des tiers publicitaires.</p>

        <h3>4. Sécurité des données</h3>
        <p>Royal LeadPro met en œuvre des mesures de sécurité robustes :</p>
        <ul>
            <li>Chiffrement SSL/TLS pour tous les transferts de données.</li>
            <li>Sauvegardes quotidiennes des bases de données.</li>
            <li>Contrôle d'accès strict pour nos employés (principe du moindre privilège).</li>
            <li>Monitoring 24/7 de l'infrastructure.</li>
        </ul>

        <h3>5. Cookies et Traceurs</h3>
        <p>Nous utilisons des cookies pour :</p>
        <ul>
            <li>Maintenir votre session active (Cookies essentiels).</li>
            <li>Mémoriser vos préférences d'interface.</li>
            <li>Analyser l'audience du site (Analytics anonymisé).</li>
        </ul>
        <p>Vous pouvez gérer vos préférences de cookies via les paramètres de votre navigateur.</p>

        <h3>6. Vos droits (RGPD & Lois locales)</h3>
        <p>Vous disposez des droits suivants sur vos données : accès, rectification, suppression, portabilité et limitation
            du traitement.</p>
        <p>Pour exercer ces droits ou pour toute question relative à vos données, contactez notre DPO à : <a
                href="mailto:privacy@royalleadpro.com">privacy@royalleadpro.com</a>.</p>

        <h3>7. Évolution de la politique</h3>
        <p>Cette politique peut être mise à jour. La dernière version sera toujours disponible sur cette page. En continuant
            d'utiliser le service après une modification, vous acceptez la nouvelle politique.</p>

        <p class="text-sm text-slate-400 mt-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>
    </div>
@endsection