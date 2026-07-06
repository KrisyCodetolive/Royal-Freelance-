# Guide de test manuel — Modules 4/5/6 (Partage, Rôles workspace, Dashboard SaaS)

> Branche testée : `feature/module6-dashboard-saas` (contient 4, 5 et 6 empilés — pas encore pushée).
> Objectif : valider à la main tout ce qui a été construit avant fusion dans `royalLeadPro`.
> Avant de commencer : `git checkout feature/module6-dashboard-saas` puis `php artisan migrate` (nouvelle colonne `can_edit`) et `php artisan db:seed --class=DatabaseSeeder` (nouveaux rôles `editor`/`viewer`, idempotent).

---

## Comptes de test (tenant dédié "QA Modules 4-5-6", plan Starter)

Mot de passe identique pour tous : **`password`**

| Rôle | Email |
|---|---|
| Super Admin (plateforme) | `admin@royal.com` |
| Owner | `qa.owner@royalleadpro.local` |
| Admin | `qa.admin@royalleadpro.local` |
| Editor | `qa.editor@royalleadpro.local` |
| Viewer | `qa.viewer@royalleadpro.local` |

3 tunnels pré-créés sur ce tenant pour les tests de portée (section 5) :
- **"QA Tunnel assigné Editor"** — `assigned_to` = Editor (modifiable par lui)
- **"QA Tunnel partagé"** — partagé avec Editor (édition) et Viewer (lecture seule)
- **"QA Tunnel privé"** — ni assigné ni partagé (invisible pour Editor/Viewer)

---

## 1. Partage de tunnels — individuel (Module 4)

1. Connecté en Owner/Admin, ouvre un tunnel existant → bouton **"Partager"** doit être visible (en haut de la page).
2. Clique dessus → choisis **"Membre spécifique"**, sélectionne un membre du tenant, laisse "Autoriser la modification" **désactivé**, valide.
   - ✅ attendu : notification "Tunnel partagé", le membre apparaît maintenant dans la liste des partages du tunnel avec un badge lecture seule (icône œil).
3. Recommence avec un autre membre, cette fois **coche "Autoriser la modification"**.
   - ✅ attendu : ce membre a le badge édition (icône crayon).
4. Connecté avec le compte du 1er membre (partagé en lecture seule) → le tunnel doit être **visible** dans sa liste de tunnels mais **non modifiable** (pas de bouton Modifier, ou accès refusé si on force l'URL `/edit`).
5. Connecté avec le compte du 2ᵉ membre (partagé en édition) → le tunnel doit être **modifiable**.

## 2. Partage de tunnels — par groupe (Module 4)

1. Crée un groupe de commerciaux (`/admin/commercial-groups`) avec 1-2 membres dedans.
2. Depuis le tunnel (bouton "Partager") → choisis **"Groupe de commerciaux"**, sélectionne ce groupe, valide.
3. Connecté avec un membre du groupe → le tunnel doit apparaître dans sa liste.
4. Recommence l'inverse : depuis la fiche du groupe (`/admin/commercial-groups/{id}`, onglet tunnels) → assigne un tunnel directement depuis là.
   - ✅ attendu : même résultat, les deux points d'entrée modifient la même relation.

## 3. Quota "tunnels partagés" — enforcement (Module 4)

Utilise un tenant en plan **Gratuit** (0 tunnel partagé autorisé) :

1. Tente de partager un tunnel (individuel ou groupe) → doit être **bloqué** avec une notification "Limite de tunnels partagés atteinte", pas de crash.
2. Passe ce tenant en **Starter** (5 tunnels partagés autorisés) → réessaie → doit passer.
3. Partage 5 tunnels différents (limite atteinte) → tente un 6ᵉ tunnel **différent** → bloqué.
4. Sur un tunnel **déjà partagé** (un des 5), ajoute un 2ᵉ ou 3ᵉ membre dessus → ne doit **jamais être bloqué**, même à la limite (un tunnel déjà partagé ne recompte pas).

## 4. Invitation avec les nouveaux rôles Editor/Viewer (Module 5)

1. `/admin/tenant-invitations` → Create → le `Select` de rôle doit maintenant proposer **4 choix** : Administrateur, Editor, Viewer, Commercial.
2. Génère une invitation en rôle **Editor**, ouvre le lien en navigation privée, complète l'inscription.
   - ✅ attendu : redirection vers `/admin` (pas l'espace commercial).
3. Recommence avec le rôle **Viewer** → même chose, redirection vers `/admin`.
4. Connecte-toi directement (login classique, pas invitation) avec le compte Editor puis Viewer créés → doivent atterrir sur `/admin` à chaque connexion.

## 5. Portée des rôles Editor/Viewer sur les tunnels (Module 5)

Utilise directement les 3 tunnels pré-créés sur le tenant QA (voir "Comptes de test" ci-dessus) : "QA Tunnel assigné Editor", "QA Tunnel partagé" (Editor en édition, Viewer en lecture seule), "QA Tunnel privé".

1. Connecté en **Editor** (`qa.editor@royalleadpro.local`) → `/admin/funnels` ne doit afficher **que** les 2 premiers tunnels (pas "QA Tunnel privé").
2. Connecté en **Admin/Owner** → doit voir **tous** les tunnels du tenant, y compris ceux non assignés à personne.
3. Sur le tunnel assigné à l'Editor → bouton "Modifier" visible, page `/edit` accessible.
4. Sur un tunnel partagé avec l'Editor en **lecture seule** (`can_edit` décoché à l'étape 1 du test 1) → pas de bouton Modifier.
5. Editor : bouton **"Partager"** et bouton **"Créer une Page"** ne doivent **jamais** apparaître, même sur un tunnel qu'il peut éditer (partager/créer une page = privilège Admin/Owner selon le CDC — à vérifier : "Créer une Page" doit être visible si le tunnel est éditable par lui, "Partager" jamais).
6. Editor : tente de créer un nouveau tunnel (`/admin/funnels/create`) → doit être bloqué (bouton "Nouveau" absent ou accès refusé).
7. Editor : tente de supprimer un tunnel → action supprimer absente/bloquée, même sur un tunnel qui lui est assigné.

## 6. Portée des rôles Editor/Viewer sur les leads (Module 5)

1. Connecté en **Editor** → `/admin/leads` ne doit afficher que les leads des tunnels qui lui sont accessibles.
2. Connecté en **Viewer** → même restriction, mais en plus **aucune** action de modification possible sur un lead (même si le tunnel est partagé "en édition" avec quelqu'un d'autre — un Viewer reste toujours lecture seule).
3. Viewer : tente de créer/supprimer un lead → bloqué.

## 7. Viewer — lecture seule stricte, même avec un partage "édition" (Module 5, bug trouvé et corrigé)

1. Partage un tunnel avec un compte **Viewer** en cochant **"Autoriser la modification"** (cas volontairement contradictoire).
2. Connecté en Viewer → ce tunnel ne doit **quand même pas** être modifiable (le rôle Viewer prime toujours sur le pivot de partage — c'est le bug corrigé pendant les tests).

## 8. Dashboard SaaS — widget d'usage (Module 6)

1. Connecte-toi (n'importe quel rôle ayant accès au panel : Owner, Admin, Editor, Viewer) → sur `/admin`, onglet "Vue d'ensemble", le tout premier bloc doit être **"Utilisation du plan"** avec 4 compteurs (Tunnels, Listes mailing, Leads, Tunnels partagés) et leur barre de progression.
2. Sur un tenant **Prestige** (illimité) → les compteurs illimités doivent afficher `∞`, pas de barre de progression pour eux.
3. Amène un tenant en plan **Gratuit** à sa limite de tunnels (2/2) → la barre du compteur "Tunnels" doit passer en **orange**.

## 9. CTA upgrade contextuel (Module 6)

1. Toujours avec un tenant à la limite (test précédent), connecté en **Owner** → un bouton **"Passer à un plan supérieur"** doit apparaître à côté du titre du widget, qui renvoie vers `/admin/my-subscription`.
2. Connecté en **Admin/Editor/Viewer** du même tenant (à sa limite) → **pas de bouton**, mais un message texte "Limite bientôt atteinte — contactez votre Owner pour upgrader".
3. Sur un tenant **loin** de ses limites → ni bouton ni message, juste les compteurs normaux.

## 10. Non-régression — Module 2/3 toujours intacts

1. `/admin/my-subscription` (Owner) → doit toujours afficher le plan courant + l'usage, et permettre de changer de plan (mécanisme inchangé, juste la donnée d'usage vient maintenant d'une méthode partagée avec le widget du Module 6).
2. Quotas tunnels/listes mailing toujours bloqués à la création comme avant (cf. guide Modules 1/2/3, section 7).

## 11. Suite automatisée (filet de sécurité, ne remplace pas le test manuel)

```bash
php artisan test
```
✅ attendu : 67 tests, tout vert (état au dernier commit de `feature/module6-dashboard-saas`).

---

## Une fois ce guide passé

- Si tout est bon → possibilité de fusionner les 3 branches (`feature/module4-partage-tunnels`, `feature/module5-roles-workspace`, `feature/module6-dashboard-saas`) dans `royalLeadPro`, puis de pousser.
- Si des bugs sont trouvés → les consigner dans `ROADMAP_SAAS_PHASE3.md` (nouvelle section "Retours de tests utilisateur — Modules 4/5/6") avant correction, comme pour les modules précédents.
