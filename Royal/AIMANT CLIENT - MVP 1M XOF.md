# AIMANT CLIENT
## MVP Optimisé - Budget 1 Million XOF

---

**Porteur de projet** : Royal  
**Date de réception** : 24 Décembre 2024  
**Budget total MVP** : 1 000 000 XOF  
**Durée MVP** : 6-8 semaines

---

# 1. SYNTHÈSE EXÉCUTIVE

**AIMANT CLIENT** est une plateforme SaaS permettant de créer et piloter des tunnels de vente/prospection pour formations, livres et programmes.

## Approche MVP Optimisée

Nous concentrons les ressources sur les **fonctionnalités critiques** pour lancer rapidement et itérer avec les retours utilisateurs.

---

# 2. PÉRIMÈTRE MVP (FONCTIONNALITÉS ESSENTIELLES)

## 2.1 Back-Office Administrateur

| Fonctionnalité | Détail |
|----------------|--------|
| **Gestion Tunnels** | Créer, modifier, dupliquer, archiver |
| **Page Builder simple** | Éditeur blocs basique (titre, texte, vidéo, bouton) |
| **Configuration tunnel** | Nom, offre, prix, lien WhatsApp, branding |
| **Gestion équipe** | Créer comptes commerciaux, assigner tunnels |
| **Dashboard analytics** | Trafic, conversions, prospects par statut |

## 2.2 Dashboard Commercial

| Fonctionnalité | Détail |
|----------------|--------|
| **Mes tunnels** | Liste des tunnels assignés |
| **Pipeline leads** | Prospects par statut (Froid → Client) |
| **Fiches prospects** | Nom, email, téléphone, score, historique |
| **Alertes** | Prospects chauds, inactivité |
| **Actions rapides** | Changer statut, ajouter tag, contacter |

## 2.3 Pages Publiques (Tunnels)

| Page | Contenu |
|------|---------|
| **Page Capture** | Formulaire (nom, email, téléphone) |
| **Page Merci** | Message + vidéo bienvenue |
| **Pages Présentation** | 3-5 vidéos en playlist |
| **Page Modalités** | Vidéo + bouton WhatsApp + CTA |

## 2.4 Tracking & Scoring

| Événement | Détail |
|-----------|--------|
| **Page views** | Visites pages tunnel |
| **Form submit** | Inscription formulaire |
| **Video play/progress** | Lecture vidéo (25%, 50%, 75%, 100%) |
| **CTA clicks** | Clics boutons WhatsApp/paiement |
| **Conversion** | Achat/adhésion confirmé |

**Scoring automatique** :
- Visite page : +1
- Inscription : +10
- Vidéo 50% : +5
- Vidéo 100% : +15
- Clic WhatsApp : +25
- Achat : +100

**Statuts** : FROID (0-10) | TIÈDE (11-30) | CHAUD (31-60) | ULTRA CHAUD (61+)

## 2.5 Alertes & Notifications

- ✅ Prospect devient CHAUD
- ✅ Clic WhatsApp détecté
- ✅ Inactivité 7 jours
- ✅ Nouvelle inscription

---

# 3. ARCHITECTURE TECHNIQUE (OPTIMISÉE)

## 3.1 Stack simplifié

| Composant | Technologie |
|-----------|-------------|
| **Frontend** | React + TailwindCSS |
| **Back-Office** | Filament 4 |
| **Backend** | Laravel 12 |
| **Base de données** | MySQL |
| **Cache** | Redis |
| **Stockage** | Cloudflare R2 |
| **Vidéos** | YouTube non listé |
| **Emails** | Resend |
| **Hébergement** | VPS unique |

---

# 4. PLANNING MVP (6-8 semaines)

| Semaine | Sprint | Livrables |
|---------|--------|-----------|
| **S1-S2** | Cadrage & Design | Architecture, maquettes, BDD |
| **S3-S4** | Backend Core | API Auth, Tunnels, Pages, Leads |
| **S5-S6** | Interfaces | Back-Office, Dashboard Commercial, Pages publiques |
| **S7** | Tracking & Scoring | Événements, scoring, alertes |
| **S8** | Tests & Lancement | QA, déploiement, documentation |

---

# 5. PROPOSITION FINANCIÈRE

## 5.1 Budget MVP (1 000 000 XOF)

| Poste | Description | Montant |
|-------|-------------|---------|
| **Cadrage & Design** | Architecture, maquettes | 100 000 XOF |
| **Backend API Core** | Auth, Tunnels, Pages, Leads | 300 000 XOF |
| **Back-Office Admin** | Interface administration | 150 000 XOF |
| **Dashboard Commercial** | Interface commerciaux | 100 000 XOF |
| **Pages Publiques** | Rendu pages, tracking | 150 000 XOF |
| **Scoring & Alertes** | Système scoring, notifications | 100 000 XOF |
| **Tests & Déploiement** | QA, mise en production | 100 000 XOF |

| **TOTAL MVP** | | **1 000 000 XOF** |

## 5.2 Échéancier de paiement

| Jalon | % | Montant | Condition |
|-------|---|---------|-----------|
| Signature | 60% | 600 000 XOF | À la commande |
| Livraison MVP | 40% | 400 000 XOF | Recette définitive |

---

# 6. LIVRABLES MVP

✅ Plateforme Web responsive (mobile + desktop)  
✅ Back-Office Administrateur  
✅ Dashboard Commercial  
✅ Pages publiques (tunnels)  
✅ Backend API + Base de données  
✅ Tracking & Scoring automatique  
✅ Documentation technique  
✅ Code source (accès Git)  
✅ Formation utilisateur (4h)  
✅ Support technique 1 mois  

---

# 7. MISES À JOUR CONTINUES (Post-MVP)

Après le lancement MVP, nous proposons un plan de mises à jour continues pour enrichir la plateforme progressivement.


# 8. COÛTS D'INFRASTRUCTURE


## 8.1 Hébergement VPS Tout-en-un

| Formule | Caractéristiques | Coût Annuel |
|---------|-----------------|-------------|
| **VPS Standard** | 4 vCPU, 8 GB RAM, 200 GB SSD, tout inclus | **350 000 XOF/an** |

## 8.2 Services tiers (estimations mensuelles)

| Service | Coût estimé |
|---------|-------------|
| Emails transactionnels (Resend) | ~5 000 XOF/mois |
| Stockage vidéos (Bunny.net) | ~10 000 XOF/mois |
| Domaine .com | ~15 000 XOF/an |

---

# 9. AVANTAGES DE CETTE APPROCHE

✅ **MVP rapide** - Lancé en 2 mois  
✅ **Budget maîtrisé** - 1M XOF pour un outil complet  
✅ **Itération agile** - Mises à jour continues selon besoins  
✅ **ROI rapide** - Commencez à générer des revenus avant V2  
✅ **Risque réduit** - Validez le marché avant investir davantage  
✅ **Flexibilité** - Adaptez les phases selon feedback utilisateurs  

---

# 10. PROCHAINES ÉTAPES

1. **Validation** de cette proposition
2. **Signature du contrat** MVP
3. **Acompte 60%** (600 000 XOF)
4. **Kick-off** et démarrage Sprint 1
5. **Livraison MVP** en 2 mois
6. **Planification Phase 1** (Email Marketing)

---

*Document confidentiel – Projet AIMANT CLIENT*  
*GENIUS GROUPS – Décembre 2024*
