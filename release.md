# Notes de Mise à Jour & Corrections

Voici la consolidation de toutes les améliorations et corrections apportées à la plateforme (Builder, Formulaires et Espace Commercial) :

## 1. Éditeur de Pages (Page Builder)
*   **Popup dynamique :** Le popup de capture obligatoire affiché en dur a été supprimé du code (`page.blade.php`). Son affichage est désormais géré depuis les paramètres du tunnel via les blocs du builder.
*   **Sauts de ligne facilités :** 
    *   **Bloc Texte :** Un appui sur "Entrée" génère désormais automatiquement un vrai saut de ligne (`<br>`) sur la page publique.
    *   **Bloc Titre :** Le champ est passé en zone de texte large, acceptant les retours à la ligne naturels.
*   **Support du format Markdown (Listes) :** 
    *   Les listes à puces (avec `- ` ou `* `) et listes numérotées (`1. `) sont désormais générées automatiquement. Le plugin CSS Tailwind Typography a été intégré pour garantir un affichage propre (puces rondes, bons espacements).
    *   **Boutons Raccourcis :** Ajout des boutons `+ Liste à puces` et `+ Liste numérotée` au-dessus de l’éditeur de texte pour insérer automatiquement le bon format du premier coup.
*   **Duplication Intelligente :** Désormais, lorsqu'on duplique un élément ou un composant (même dans des colonnes), le clone est inséré de façon logique **immédiatement en dessous de l'original**, au lieu d'être repoussé tout en bas de la page.
*   **Fonction "Annuler" les modifications :** Un historique de session temporaire a été ajouté. Un bouton "Annuler" dans la barre d'action du composant permet d'effacer en un clic toutes les modifications (texte, design) effectuées depuis l'ouverture du bloc, et ce sans avoir à le supprimer puis le recréer.
*   **Fiabilisation des marges et tailles :** Les curseurs (ranges) pour la taille de police, l'espacement des lettres, et l'arrondi des bords ont été remplacés par des champs textes ouverts. On peut désormais y taper explicitement la valeur avec son unité (`16px`, `2rem`, `50%`), ce qui évite les conflits d’interprétation de Livewire.

## 2. Éditeur de Formulaires
*   **Édition du Titre :** Ajout d'un champ tout en haut de l'éditeur de formulaire permettant de modifier son Titre librement.
*   **Gestion des Options (Cases, Radios, Sélecteurs) :** 
    *   L’éditeur ne permettait pas de créer ses propres choix de réponse. 
    *   Un champ dynamique "*Options (séparées par des virgules)*" apparaît désormais si l'on choisit "Case à cocher", "Liste déroulante" ou "Bouton radio".
    *   Les options configurées sont instantanément répercutées dans l'aperçu du builder et sur la page web publique.
*   **Rajout du Bouton Radio :** L'option "*Bouton radio (Choix unique)*", qui manquait dans le sélecteur, a été rajoutée et configurée.

## 3. Espace Commercial & Système de Tracking
*   **Fiabilité de la copie du "Lien Unique" :** Le bouton servant à copier le lien commercial posait des soucis sur les navigateurs hors protocole HTTPS (comme en local). Un système de repli natif (`execCommand`) a été ajouté ; la copie marchera dans n'importe quel contexte de sécurité.
*   **Mise à jour du Nom de Boutique & Sous-domaine :**
    *   Le formulaire de profil n'enregistrait pas bien les champs que l'on vidait volontairement (restauration silencieuse de la valeur précédente). Ceci a été patché.
    *   Par ailleurs, en changeant de nom de boutique, le lien sous-domaine associé (`votre-boutique.royalleadpro.com`) restait figé (le système refusait de le recalculer). Cette restriction de la base de données a été levée, le sous-domaine et le lien se mettent désormais dynamiquement à jour au changement de nom.
*   **Attribution des Leads & Tunnels parfaits :** 
    *   **Problème initial :** Lorsqu'un visiteur remplissait un formulaire via le sous-domaine personnalisé d'un commercial (`commercial.domaine.com/f/tunnel`), les leads tombaient dans une faille et n'étaient pas attribués à ce commercial.
    *   **Correctif complet :** Le contrôleur (`FunnelController` via la méthode `resolveFunnel`) analyse désormais adéquatement les sous-domaines hôtes en le différenciant des sous-domaines des commerciaux. Que ce soit via son URL unique par défaut ou son propre domaine, le tracking rattache le visiteur au commercial lié à cette session en un temps record.
    *   Tout soumission de formulaire crédite immédiatement le lead dans la section "Mes Leads" de ce commercial.
*   **Correction de l'attribution WhatsApp :** Correction d'un bug où le bouton WhatsApp des tunnels utilisait toujours le numéro du propriétaire au lieu de celui du commercial. Le système utilise désormais par défaut le numéro configuré dans le profil du commercial, avec la possibilité de définir un message spécifique par tunnel.