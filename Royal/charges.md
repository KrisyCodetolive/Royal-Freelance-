1) Vision du projet (AIMANT CLIENT)
Objectif : créer une plateforme web qui permet de construire, dupliquer et piloter des tunnels de vente/prospection (comme sur ton image) pour :
tes formations


tes livres


tes programmes (communautés / coaching / MLM)


Résultat attendu :
1 tunnel = 1 offre (formation ou livre)


un prospect entre via une page capture


reçoit une séquence emails/WhatsApp


consomme des vidéos/pages


clique sur des boutons CTA


finit sur WhatsApp / paiement / inscription


tout est tracké (KPI + tags + alertes)



2) Fonctionnalités obligatoires (MVP)
A. Module “Tunnels”
Créer / modifier / dupliquer / archiver un tunnel


Modèles prédéfinis (template “MLM / Formation / Livre”)


Nom du tunnel, offre, prix, lien WhatsApp, lien paiement, branding


B. Pages du tunnel (builder simple)
Pages types à générer :
Page Capture (formulaire)


Page Merci / Bienvenue (Vidéo 0)


Page Présentation (Vidéos 1 à 5, format playlist)


Page Modalités / Adhésion (Vidéo 6 + bouton WhatsApp)


Pages optionnelles : FAQ / Témoignages / Paiement


Builder attendu :
édition blocs (Titre, texte, vidéo, bouton, image)


choix couleurs + logo


responsive mobile


C. Leads & Pipeline
Liste prospects par tunnel


Fiche prospect : email, téléphone, source, tags, score, historique (pages vues, vidéos vues, clics)


Statuts : FROID / TIEDE / CHAUD / ULTRA CHAUD / CLIENT / MEMBRE


Import/export CSV


D. Tracking & Analytics
À tracker automatiquement :
visiteurs page capture


conversion formulaire


ouvertures emails


clics emails


vidéos : play / 25% / 50% / 75% / 100%


clic bouton WhatsApp


clic bouton Paiement


conversion finale (achat/adhésion) (manuel ou automatique via webhook)


E. Tags + Alertes automatiques
Tags automatiques selon actions


Alertes (notifications) quand :


prospect “chaud” (ex: 3 vidéos en 24h)


clic WhatsApp sans message


inactif 7 jours / 14 jours


Relances : email / WhatsApp (semi-auto)



3) Fonctionnalités recommandées (V2)
Email marketing intégré (séquences par tunnel)


WhatsApp automation via liens “click-to-chat” + messages préremplis, ou intégration API WhatsApp Business (si tu l’utilises)


Paiements : Stripe / Paystack / CinetPay / Wave (selon pays) + webhooks


A/B testing page capture et CTA


Multi-utilisateurs (Admin / Manager / Agent)


Bibliothèque médias (vidéos, images, PDFs)


Générateur de liens affiliés (si tu veux Royal Freelance / revendeurs)



4) Architecture technique conseillée
Stack (exemple solide)
Frontend : Next.js / React


Backend API : Node.js (NestJS) ou Laravel


DB : PostgreSQL


Cache/queue : Redis (emails/notifications)


Storage : S3 compatible (Wasabi, Cloudflare R2) pour médias


Video : YouTube non listé / Vimeo / stockage privé + lecteur


Analytics : Event tracking interne + Pixel Meta + Google Tag Manager


Auth : JWT + rôles



5) Modèle de données minimum (simple)
Users (admin, team)


Offers (formation/livre)


Funnels (1 offre = 1 funnel ou plusieurs)


Pages (capture/merci/presentation/modalites)


Leads


Events (page_view, video_progress, email_open, email_click, cta_click, conversion)


Tags


LeadTags


Automations (règles : “si event X alors tag/alerte/message”)



6) Règles de scoring (à intégrer)
Exemple (modifiable dans l’admin) :
Visite page capture : +1


Inscription : +10


Vidéo 1 vue 50% : +5


Vidéo 3 complétée : +15


Clic WhatsApp : +25


Réponse WhatsApp (manuel) : +20


Achat/adhésion : +100


Seuils :
0–10 froid


11–30 tiède


31–60 chaud


61+ ultra chaud



7) Dashboard (écran obligatoire)
Par tunnel :
trafic


conversion capture


taux visionnage vidéos (par vidéo)


taux clic WhatsApp


taux conversion finale


prospects par niveau (froid/tiède/chaud)


top sources (Facebook, TikTok, WhatsApp, etc.)


alertes du jour (prospects à relancer)



