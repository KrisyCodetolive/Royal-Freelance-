# Guide de test manuel — Module 7 (Super Admin Royal LeadPro)

> Branche testée : `feature/module7-super-admin` (pas encore pushée, pas encore fusionnée dans `royalLeadPro`).
> Objectif : valider à la main la gestion des tenants côté super_admin (liste, fiche, usage, suspension/réactivation) et l'analytics SaaS globale, avant fusion.
> Avant de commencer : `git checkout feature/module7-super-admin` puis lance ton serveur local habituel (`php artisan serve` ou équivalent).
> Les comptes et données ci-dessous ont déjà été créés en base (via tinker) — rien à seeder, connecte-toi directement.

---

## Comptes de test

Mot de passe identique pour tous : **`password`**

| Rôle | Email | Portée |
|---|---|---|
| Super Admin (plateforme) | `admin@royal.com` | Toute la plateforme |
| Owner | `qa.m7.owner@royalleadpro.local` | Tenant "QA Module 7 Suspension" (plan Starter) |
| Admin | `qa.m7.admin@royalleadpro.local` | Tenant "QA Module 7 Suspension" (plan Starter) |

Un tunnel publié existe sur ce tenant, accessible via sous-domaine :
`http://qa-m7-tunnel.localhost:<PORT>/` (remplace `<PORT>` par le port de ton `php artisan serve`).

---

## 1. Liste des tenants (super_admin)

1. Connecte-toi avec `admin@royal.com` → menu "Super Admin" dans la sidebar → **Tenants**.
2. ✅ attendu : le tenant "QA Module 7 Suspension" apparaît dans la liste, avec email, propriétaire (Owner : QA M7 Owner), plan (Starter), nombre de membres (2), et une icône "Actif" verte / "Suspendu" grise.
3. Le badge de navigation à côté de "Tenants" doit afficher le nombre total de tenants de la plateforme.

## 2. Fiche tenant — informations et usage

1. Depuis la liste, clique sur le tenant "QA Module 7 Suspension" (icône œil ou nom).
2. ✅ attendu : 4 sections — **Entreprise**, **Propriétaire (Owner)**, **Abonnement** (plan Starter, statut actif), **Usage & quotas** (badges Tunnels/Listes mailing/Leads/Tunnels partagés, chacun "0 / limite du plan Starter"), **Activité** (2 membres, Actif ✅, Suspendu ❌).
3. Les badges d'usage doivent être verts (aucun quota proche de la limite).

## 3. Suspension d'un tenant

1. Sur la fiche du tenant, clique sur le bouton **"Suspendre"** (rouge, en haut).
2. Une modale de confirmation doit apparaître, expliquant l'effet (panel + tunnels publics coupés). Confirme.
3. ✅ attendu : notification "Tenant suspendu", le bouton devient "Réactiver" (vert), la section Activité affiche maintenant "Suspendu" en rouge.
4. **Vérifie l'effet côté tenant** : déconnecte-toi, connecte-toi avec `qa.m7.owner@royalleadpro.local` ou `qa.m7.admin@royalleadpro.local` (mot de passe correct).
   - ✅ attendu : la connexion elle-même réussit (le login ne vérifie que l'identité, pas le statut du tenant) et redirige vers `/admin`, mais cette page renvoie une **erreur 403 "Forbidden"** — c'est Filament qui bloque l'accès au panel via `canAccessPanel()`, pas le formulaire de login.
5. **Vérifie l'effet sur le tunnel public** : ouvre `http://qa-m7-tunnel.localhost:<PORT>/` dans le navigateur (déconnecté, navigation privée si besoin).
   - ✅ attendu : page 404 — le tunnel n'est plus accessible publiquement.
6. Reconnecte-toi en `admin@royal.com` → même action est aussi disponible directement depuis la **liste** des tenants (bouton "Suspendre" sur la ligne), pas seulement depuis la fiche — à tester une fois sur un autre tenant si tu veux confirmer la cohérence des deux points d'entrée.

## 4. Réactivation d'un tenant

1. Sur la fiche du tenant suspendu, clique sur **"Réactiver"** (vert), confirme.
2. ✅ attendu : notification "Tenant réactivé", le badge "Suspendu" repasse à gris/désactivé.
3. Reconnecte-toi avec `qa.m7.owner@royalleadpro.local` → ✅ doit fonctionner à nouveau, redirection vers `/admin`.
4. Recharge `http://qa-m7-tunnel.localhost:<PORT>/` → ✅ doit à nouveau afficher la page du tunnel (plus de 404).

## 5. Analytics SaaS globale

1. Connecté en `admin@royal.com` → menu "Super Admin" → **Analytics SaaS**.
2. ✅ attendu : 3 blocs —
   - Stats en haut : nombre total de tenants, tenants actifs (avec le nombre de suspendus s'il y en a), MRR mocké en FCFA.
   - Un graphique en anneau : répartition des tenants par plan (Gratuit/Starter/Prestige).
   - Un graphique en courbe : nouveaux tenants sur les 30 derniers jours.
3. Suspends puis réactive à nouveau le tenant QA (section 3/4) et recharge cette page entre les deux : le compteur "Tenants actifs" / "suspendu(s)" doit refléter le changement en temps réel.

## 6. Restriction d'accès — un tenant ne doit rien voir du Super Admin

1. Connecte-toi avec `qa.m7.owner@royalleadpro.local` (Owner, pas super_admin).
2. ✅ attendu : le menu "Super Admin" (Tenants, Analytics SaaS, Abonnements) **n'apparaît pas** dans la sidebar.
3. Tente d'accéder directement à `/admin/tenants` et `/admin/saas-analytics` en tapant l'URL.
   - ✅ attendu : accès refusé (403 ou redirection), pas de fuite de données d'autres tenants.

---

## Notes

- Les comptes QA ci-dessus sont volontairement laissés en base (pas de nettoyage automatique) pour permettre de rejouer ces tests plusieurs fois. À supprimer manuellement si besoin avant une mise en prod.
- Le gap connu du Module 7 (non testé ici, hors scope confirmé) : l'espace commercial (`/commercial/*`) n'est pas bloqué par la suspension d'un tenant — un commercial de ce tenant pourrait encore se connecter à son propre dashboard même tenant suspendu. Voir `ROADMAP_SAAS_PHASE3.md`, section Module 7.
