# Guide de test manuel — Modules 1/2/3 + Owner/Plan/Self-service

> Branche testée : `feature/owner-role-plan-selection` (pas encore pushée).
> Objectif : valider à la main tout ce qui a été construit avant de passer au Module 4 ou 5.
> Compte super_admin existant en base locale : `admin@royal.com` / `password`.

---

## 1. Inscription self-service d'un nouveau tenant (`/demarrer`)

1. Va sur `/demarrer` (déconnecté).
2. **Étape 1 — identification** : nom d'entreprise, nom/email/mot de passe de l'admin. Valide.
   - ✅ attendu : passe à l'étape 2 sans créer le compte tout de suite.
3. **Étape 2 — choix du plan** : les 3 plans (Gratuit / Starter / Prestige) doivent s'afficher, y compris les payants.
   - Teste d'abord avec **Gratuit** : valide.
   - ✅ attendu : redirection connectée vers `/admin`, un tenant + une `Subscription` (plan `free`, statut `active`, `ends_at` = null) créés.
4. Recommence tout le flow avec un 2ᵉ compte en choisissant **Starter** (mensuel) puis un 3ᵉ en **annuel**.
   - ✅ attendu : `Subscription.ends_at` calculé (~+1 mois / ~+1 an à partir d'aujourd'hui), pas de vraie page de paiement (mock).
5. Vérifie que le mot de passe n'est jamais en clair : `php artisan tinker` → `User::where('email','...')->first()->password` doit être un hash bcrypt, pas la valeur saisie.

## 2. Isolation multi-tenant

1. Avec les 2-3 tenants créés à l'étape 1, connecte-toi sur chacun et vérifie dans `/admin` (Tunnels, Leads, Utilisateurs) qu'**aucune donnée d'un autre tenant n'apparaît**.
2. Sur le dashboard admin (widgets stats), vérifie que les chiffres agrégés sont bien scopés au tenant courant (c'est le bug des 6 widgets déjà corrigé — sert de non-régression).

## 3. Redirection role-aware après connexion

1. Connecte-toi avec le compte Owner créé en étape 1 → doit atterrir sur `/admin` (pas `/commercial`).
2. Sur la page d'accueil publique, bouton **"Mon espace"** avec ce compte connecté → doit pointer vers `/admin` (non-régression du bug `isSuperAdmin()` vs `isAdmin()`).
3. Connecte-toi avec `admin@royal.com` (super_admin) → `/admin`, bouton "Mon espace" → `/admin` aussi.

## 4. Invitation d'un membre (choix du rôle)

1. Connecté en Owner (ou Admin), va dans `/admin/tenant-invitations` → **Create**.
2. Vérifie que le formulaire propose un **Select de rôle : Admin ou Commercial** (plus de rôle forcé en dur).
3. Génère une invitation pour un email de test avec rôle **Admin**.
4. Ouvre le lien généré (`/register?invitation=TOKEN`) en navigation privée.
   - ✅ attendu : formulaire d'inscription commercial, le token est bien passé.
5. Complète l'inscription → vérifie que l'utilisateur créé :
   - est bien rattaché au **bon tenant** (celui de l'invitation, pas `Tenant::first()`),
   - a le rôle **Admin** attribué,
   - est redirigé vers **`/admin`** (pas `/commercial`, puisqu'invité comme admin).
6. Recommence avec une invitation **rôle Commercial** → vérifie redirection vers l'espace commercial cette fois.
7. Essaie de réutiliser le même lien d'invitation une 2ᵉ fois → doit être refusé (token déjà utilisé).
8. Essaie un token invalide/expiré manuellement (modifier l'URL) → message d'erreur "lien invalide ou expiré", pas de crash.

## 5. Billing — vue super_admin (`SubscriptionResource`)

1. Connecté en `admin@royal.com` (super_admin) → `/admin/subscriptions` doit être accessible.
2. Connecté en Owner/Admin d'un tenant (pas super_admin) → `/admin/subscriptions` ne doit **pas** apparaître dans le menu, et l'URL directe doit être bloquée (`canAccess()`).
3. En super_admin, ouvre une subscription existante → change le plan d'un tenant manuellement, sauvegarde.
   - ✅ attendu : les quotas de ce tenant reflètent immédiatement le nouveau plan.

## 6. Self-service "Mon abonnement" — réservé à l'Owner

1. Connecté en **Owner** → menu doit afficher **"Mon abonnement"** (`/admin/my-subscription`).
2. Connecté en **Admin simple** (pas Owner) du même tenant → cette page ne doit **pas** être accessible (canAccess restreint à `hasRole('owner')`).
3. Sur la page Owner : vérifie que le plan courant + l'usage (tunnels/leads/listes utilisés vs limite) s'affichent correctement.
4. Utilise l'action **changer de plan** → même mécanisme de paiement mocké qu'à l'inscription, vérifie que la `Subscription` est bien mise à jour (nouveau plan, nouvelle `ends_at` si payant).

## 7. Quotas — enforcement à la création

Utilise un tenant en plan **Gratuit** (2 tunnels / 1 liste mailing / 1000 leads) :

1. Crée 2 tunnels → OK.
2. Tente de créer un 3ᵉ tunnel → doit être **bloqué** (Halt + notification "limite atteinte"), pas de crash serveur.
3. Crée 1 séquence email (liste mailing) → OK. Tente une 2ᵉ → bloquée pareil.
4. Passe ce tenant en **Starter** (via SubscriptionResource ou Mon abonnement) → retente de créer un tunnel/séquence → doit passer maintenant (limites relevées : 10 tunnels / 10 listes).
5. Sur un tenant **Prestige** : vérifie que les tunnels ne sont jamais bloqués (illimité, `max_tunnels = null`).
6. **Non testé automatiquement, à surveiller sans bloquer** : les leads (créés dès la 1ʳᵉ visite anonyme d'une page) ne sont **pas encore bloqués** par le quota — normal, décision assumée (cf. roadmap Module 3). Pas la peine de le signaler comme bug si tu dépasses 1000 leads en test.
7. Idem, le **partage de tunnels n'est pas bloqué** car aucune UI ne le déclenche encore (Module 4 pas construit) — normal.

## 8. Régression rapide — formulaires Filament

Ces 3 formulaires avaient un bug d'import Filament 4 (`Section`/`Grid` déplacés) qui les faisait planter à l'ouverture :

1. `/admin/tenant-invitations/create` → doit s'ouvrir sans erreur 500.
2. `/admin/subscriptions/create` (en super_admin) → idem.
3. `/admin/tags/create` → idem.

## 9. Suite automatisée (filet de sécurité, pas remplace le test manuel)

```bash
php artisan test
```
✅ attendu : 49 tests, 126 assertions, tout vert (état au dernier commit).

---

## Une fois ce guide passé

- Si tout est bon → possibilité de pousser la branche (`git push`) et ouvrir une PR vers `royalLeadPro`.
- Si des bugs sont trouvés → les consigner dans `ROADMAP_SAAS_PHASE3.md` section "Retours de tests utilisateur" avant correction (pattern déjà suivi dans ce projet).
