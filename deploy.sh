#!/bin/bash

# Script de déploiement continu ROYAL avec FTP
# Détecte automatiquement les fichiers modifiés depuis Git et les uploade
# Usage: ./deploy.sh [options]

set -e

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Configuration par défaut
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_FILE="${SCRIPT_DIR}/ftp-config"
LOG_DIR="${SCRIPT_DIR}/deploy-logs"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
LOG_FILE="${LOG_DIR}/deploy_${TIMESTAMP}.log"
LAST_DEPLOY_FILE="${SCRIPT_DIR}/.last-deploy"
FAILED_UPLOADS_FILE="${SCRIPT_DIR}/.failed-uploads"
FORCE_UPLOAD=false
DRY_RUN=false
VERBOSE=false
RETRY_FAILED=false

# Créer le répertoire de logs
mkdir -p "${LOG_DIR}"

# Fonction de logging
log() {
    local level=$1
    shift
    local message="$*"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo -e "${timestamp} [${level}] ${message}" | tee -a "${LOG_FILE}"
}

log_info() {
    log "INFO" "$*"
    echo -e "${BLUE}ℹ️  $*${NC}"
}

log_success() {
    log "SUCCESS" "$*"
    echo -e "${GREEN}✅ $*${NC}"
}

log_warning() {
    log "WARNING" "$*"
    echo -e "${YELLOW}⚠️  $*${NC}"
}

log_error() {
    log "ERROR" "$*"
    echo -e "${RED}❌ $*${NC}"
}

# Fonction d'aide
show_help() {
    cat << EOF
🚀 Script de Déploiement Continu ROYAL

Usage: ./deploy.sh [OPTIONS]

OPTIONS:
    -h, --help          Afficher cette aide
    -f, --force         Forcer l'upload de tous les fichiers
    -d, --dry-run       Mode simulation (ne fait pas l'upload)
    -v, --verbose       Mode verbeux
    -c, --config FILE   Fichier de configuration personnalisé
    --since COMMIT      Déployer depuis un commit spécifique
    --branch BRANCH     Déployer une branche spécifique
    --retry             Relancer uniquement les fichiers échoués
    --reset-ref         Réinitialiser le commit de référence à HEAD
    --clear-failed      Effacer la liste des fichiers échoués

EXEMPLES:
    ./deploy.sh                    # Déploiement normal
    ./deploy.sh --dry-run          # Simulation
    ./deploy.sh --force            # Upload complet
    ./deploy.sh --since HEAD~5     # Depuis 5 commits
    ./deploy.sh --branch develop   # Branche develop

CONFIGURATION:
    Copiez 'ftp-config.example' vers 'ftp-config' et configurez vos paramètres FTP.

EOF
}

# Parser les arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        -f|--force)
            FORCE_UPLOAD=true
            shift
            ;;
        -d|--dry-run)
            DRY_RUN=true
            shift
            ;;
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        -c|--config)
            CONFIG_FILE="$2"
            shift 2
            ;;
        --since)
            SINCE_COMMIT="$2"
            shift 2
            ;;
        --branch)
            TARGET_BRANCH="$2"
            shift 2
            ;;
        --retry)
            RETRY_FAILED=true
            shift
            ;;
        --reset-ref)
            # Réinitialiser le commit de référence au HEAD actuel
            git rev-parse HEAD > "${SCRIPT_DIR}/.last-deploy"
            echo -e "${GREEN}✅ Commit de référence mis à jour: $(git rev-parse --short HEAD)${NC}"
            exit 0
            ;;
        --clear-failed)
            # Supprimer le fichier des uploads échoués
            rm -f "${SCRIPT_DIR}/.failed-uploads"
            echo -e "${GREEN}✅ Liste des fichiers échoués effacée${NC}"
            exit 0
            ;;
        *)
            log_error "Option inconnue: $1"
            show_help
            exit 1
            ;;
    esac
done

# Vérifier que nous sommes dans un repo Git
if [ ! -d ".git" ]; then
    log_error "Ce répertoire n'est pas un dépôt Git!"
    exit 1
fi

# Charger la configuration FTP
if [ ! -f "${CONFIG_FILE}" ]; then
    log_error "Fichier de configuration FTP introuvable: ${CONFIG_FILE}"
    log_info "Copiez 'ftp-config.example' vers 'ftp-config' et configurez vos paramètres."
    exit 1
fi

log_info "Chargement de la configuration FTP..."
source "${CONFIG_FILE}"

# Vérifier les paramètres FTP obligatoires
if [[ -z "${FTP_HOST}" || -z "${FTP_USER}" || -z "${FTP_PASS}" ]]; then
    log_error "Configuration FTP incomplète! Vérifiez FTP_HOST, FTP_USER et FTP_PASS."
    exit 1
fi

# Fonction pour vérifier si un fichier doit être exclu
should_exclude() {
    local file="$1"
    for pattern in "${EXCLUDE_PATTERNS[@]}"; do
        if [[ "$file" == $pattern ]]; then
            return 0
        fi
    done
    return 1
}

# Fonction pour obtenir les fichiers modifiés
get_modified_files() {
    local files=()
    
    if [ "$FORCE_UPLOAD" = true ]; then
        log_info "Mode force activé - récupération de tous les fichiers..." >&2
        while IFS= read -r -d '' file; do
            if [ -f "$file" ] && ! should_exclude "$file"; then
                files+=("$file")
            fi
        done < <(find . -type f -not -path './.git/*' -print0)
    else
        # Déterminer le commit de référence
        local since_commit=""
        local use_last_commit_only=false
        
        if [ -n "${SINCE_COMMIT}" ]; then
            since_commit="${SINCE_COMMIT}"
        elif [ -f "${LAST_DEPLOY_FILE}" ]; then
            since_commit=$(cat "${LAST_DEPLOY_FILE}")
            # Vérifier que le commit existe toujours
            if ! git cat-file -e "${since_commit}^{commit}" 2>/dev/null; then
                log_warning "Le commit de référence ${since_commit} n'existe plus!" >&2
                log_info "Utilisation du dernier commit uniquement..." >&2
                use_last_commit_only=true
            fi
        else
            # Premier déploiement - prendre uniquement les fichiers du dernier commit
            log_info "Premier déploiement détecté - fichiers du dernier commit uniquement" >&2
            use_last_commit_only=true
        fi
        
        if [ "$use_last_commit_only" = true ]; then
            # Récupérer uniquement les fichiers du dernier commit
            log_info "Détection des fichiers du dernier commit (HEAD)..." >&2
            while IFS= read -r file; do
                if [ -f "$file" ] && ! should_exclude "$file"; then
                    files+=("$file")
                fi
            done < <(git diff-tree --no-commit-id --name-only -r HEAD 2>/dev/null)
        else
            # Log vers stderr pour éviter la capture dans le retour de fonction
            log_info "Détection des fichiers modifiés depuis: ${since_commit}" >&2
            
            # Récupérer les fichiers modifiés, ajoutés ou renommés
            while IFS= read -r file; do
                if [ -f "$file" ] && ! should_exclude "$file"; then
                    files+=("$file")
                fi
            done < <(git diff --name-only --diff-filter=AMR "${since_commit}" HEAD 2>/dev/null)
        fi
    fi
    
    printf '%s\n' "${files[@]}"
}

# Fonction pour créer un répertoire FTP avec curl
curl_ftp_mkdir() {
    local remote_dir="$1"
    
    # Méthode simple et fiable : utiliser --ftp-create-dirs
    # Cette option crée automatiquement tous les répertoires parents nécessaires
    curl --silent \
         --user "${FTP_USER}:${FTP_PASS}" \
         --ftp-create-dirs \
         "ftp://${FTP_HOST}:${FTP_PORT:-21}${remote_dir}/" >> "${LOG_FILE}" 2>&1 || true
}

# Fonction pour uploader un fichier avec curl
curl_ftp_upload() {
    local local_file="$1"
    local remote_file="$2"
    local ftp_url="ftp://${FTP_HOST}:${FTP_PORT:-21}${remote_file}"
    
    curl --silent --show-error \
         --user "${FTP_USER}:${FTP_PASS}" \
         --ftp-create-dirs \
         --upload-file "${local_file}" \
         "${ftp_url}" >> "${LOG_FILE}" 2>&1
}

# Fonction pour uploader un fichier
upload_file() {
    local local_file="$1"
    local remote_file="${FTP_REMOTE_DIR}/${local_file}"
    local remote_dir=$(dirname "$remote_file")
    
    if [ "$DRY_RUN" = true ]; then
        log_info "[DRY-RUN] Upload: ${local_file} -> ${remote_file}"
        return 0
    fi
    
    # Uploader le fichier (--ftp-create-dirs crée automatiquement les répertoires)
    if curl_ftp_upload "${local_file}" "${remote_file}"; then
        log_success "Uploadé: ${local_file}"
        return 0
    else
        log_error "Erreur upload: ${local_file}"
        return 1
    fi
}

# Fonction pour créer les répertoires distants
create_remote_directories() {
    if [ "$DRY_RUN" = true ]; then
        log_info "[DRY-RUN] Création des répertoires distants"
        return 0
    fi
    
    # Les répertoires sont créés automatiquement lors de l'upload avec --ftp-create-dirs
    log_info "Préparation des répertoires distants (création automatique)..."
}

# Fonction pour créer un backup
create_backup() {
    if [ "$CREATE_BACKUP" != true ] || [ "$DRY_RUN" = true ]; then
        return 0
    fi
    
    log_info "Création d'un backup distant..."
    
    local backup_dir="${FTP_REMOTE_DIR}/backups/backup_${TIMESTAMP}"
    
    # Créer les répertoires de backup
    curl_ftp_mkdir "${FTP_REMOTE_DIR}/backups"
    curl_ftp_mkdir "${backup_dir}"
    
    # Note: Pour un backup complet, il faudrait télécharger puis re-uploader
    # Ce qui peut être long. On se contente de créer le répertoire pour l'instant.
}

# Fonction principale
main() {
    echo -e "${PURPLE}"
    cat << "EOF"
    ╔══════════════════════════════════════════════════════════════╗
    ║             🚀 GENIUS PAY DEPLOY SCRIPT 🚀                    ║
    ║               Déploiement Continu avec FTP                   ║
    ╚══════════════════════════════════════════════════════════════╝
EOF
    echo -e "${NC}"
    
    log_info "Début du déploiement - $(date)"
    log_info "Configuration: ${CONFIG_FILE}"
    log_info "Serveur FTP: ${FTP_HOST}"
    log_info "Répertoire distant: ${FTP_REMOTE_DIR}"
    
    # Vérifier l'état Git
    if [ -z "${TARGET_BRANCH}" ]; then
        TARGET_BRANCH=$(git branch --show-current)
    fi
    
    log_info "Branche courante: ${TARGET_BRANCH}"
    
    # Vérifier s'il y a des modifications non commitées (sauf en mode retry)
    if [ "$RETRY_FAILED" != true ] && [ -n "$(git status --porcelain)" ]; then
        log_warning "Il y a des modifications non commitées!"
        if [ "$FORCE_UPLOAD" != true ]; then
            log_error "Commitez vos modifications ou utilisez --force"
            exit 1
        fi
    fi
    
    # Charger les fichiers échoués précédents s'ils existent
    local failed_from_previous=()
    if [ -f "${FAILED_UPLOADS_FILE}" ]; then
        local failed_count_prev=$(wc -l < "${FAILED_UPLOADS_FILE}" | tr -d ' ')
        if [ "$failed_count_prev" -gt 0 ]; then
            log_warning "📋 ${failed_count_prev} fichier(s) échoué(s) du déploiement précédent trouvé(s)"
            while IFS= read -r line; do
                if [ -f "$line" ]; then
                    failed_from_previous+=("$line")
                fi
            done < "${FAILED_UPLOADS_FILE}"
        fi
    fi
    
    # Récupérer les fichiers à uploader
    files_to_upload=()
    
    if [ "$RETRY_FAILED" = true ]; then
        # Mode retry : uniquement les fichiers échoués
        log_info "Mode RETRY - Relance des fichiers échoués uniquement..."
        files_to_upload=("${failed_from_previous[@]}")
    else
        # Mode normal : fichiers modifiés + fichiers échoués
        log_info "Analyse des fichiers à déployer..."
        while IFS= read -r line; do
            files_to_upload+=("$line")
        done < <(get_modified_files)
        
        # Ajouter les fichiers échoués précédents (sans doublons)
        for failed_file in "${failed_from_previous[@]}"; do
            local already_in_list=false
            for existing in "${files_to_upload[@]}"; do
                if [ "$failed_file" = "$existing" ]; then
                    already_in_list=true
                    break
                fi
            done
            if [ "$already_in_list" = false ]; then
                files_to_upload+=("$failed_file")
                log_info "  + Ajout fichier échoué: ${failed_file}"
            fi
        done
    fi
    
    if [ ${#files_to_upload[@]} -eq 0 ]; then
        log_info "Aucun fichier à déployer!"
        # Nettoyer le fichier des échecs s'il est vide
        rm -f "${FAILED_UPLOADS_FILE}"
        exit 0
    fi
    
    log_info "Fichiers à déployer: ${#files_to_upload[@]}"
    
    # Afficher la liste des fichiers qui seront déployés
    echo -e "\n${CYAN}📋 FICHIERS QUI SERONT DÉPLOYÉS:${NC}"
    for file in "${files_to_upload[@]}"; do
        local file_size=""
        if [ -f "$file" ]; then
            file_size=$(du -h "$file" | cut -f1)
        fi
        echo -e "  ${GREEN}📄${NC} $file ${YELLOW}(${file_size})${NC}"
    done
    echo ""
    
    # Confirmation
    if [ "$DRY_RUN" != true ]; then
        echo -e "\n${YELLOW}Voulez-vous continuer le déploiement? (y/N)${NC}"
        read -r response
        if [[ ! "$response" =~ ^[Yy]$ ]]; then
            log_info "Déploiement annulé par l'utilisateur"
            exit 0
        fi
    fi
    
    # Créer un backup si configuré
    create_backup
    
    # Créer les répertoires distants
    create_remote_directories
    
    # Upload des fichiers
    log_info "Début de l'upload..."
    local success_count=0
    local error_count=0
    local uploaded_files=()
    local failed_files=()
    
    for file in "${files_to_upload[@]}"; do
        if upload_file "$file"; then
            ((success_count++))
            uploaded_files+=("$file")
        else
            ((error_count++))
            failed_files+=("$file")
        fi
    done
    
    # Résumé détaillé
    echo -e "\n${CYAN}📊 RÉSUMÉ DU DÉPLOIEMENT${NC}"
    log_success "Fichiers uploadés avec succès: ${success_count}"
    
    # Afficher les fichiers uploadés avec succès
    if [ ${#uploaded_files[@]} -gt 0 ]; then
        echo -e "\n${GREEN}✅ FICHIERS ENVOYÉS AVEC SUCCÈS:${NC}"
        for file in "${uploaded_files[@]}"; do
            local file_size=""
            if [ -f "$file" ]; then
                file_size=$(du -h "$file" | cut -f1)
            fi
            echo -e "  ${GREEN}✓${NC} $file ${YELLOW}(${file_size})${NC}"
        done
    fi
    
    # Afficher les fichiers en erreur
    if [ $error_count -gt 0 ]; then
        log_error "Fichiers en erreur: ${error_count}"
        echo -e "\n${RED}❌ FICHIERS EN ERREUR:${NC}"
        for file in "${failed_files[@]}"; do
            echo -e "  ${RED}✗${NC} $file"
        done
    fi
    
    # Sauvegarder les fichiers échoués pour le prochain déploiement
    if [ "$DRY_RUN" != true ]; then
        if [ $error_count -gt 0 ]; then
            # Sauvegarder les fichiers échoués
            printf '%s\n' "${failed_files[@]}" > "${FAILED_UPLOADS_FILE}"
            log_warning "📝 ${error_count} fichier(s) échoué(s) sauvegardé(s) dans .failed-uploads"
            log_info "Utilisez './deploy.sh --retry' pour les relancer"
        else
            # Supprimer le fichier des échecs s'il n'y a plus d'erreurs
            rm -f "${FAILED_UPLOADS_FILE}"
        fi
        
        # TOUJOURS mettre à jour le commit de référence si au moins un fichier a réussi
        if [ $success_count -gt 0 ]; then
            git rev-parse HEAD > "${LAST_DEPLOY_FILE}"
            log_success "✅ Commit de référence mis à jour: $(git rev-parse --short HEAD)"
        fi
        
        if [ $error_count -eq 0 ]; then
            log_success "🎉 Déploiement terminé avec succès!"
        else
            log_warning "⚠️  Déploiement partiel: ${success_count} réussi(s), ${error_count} échoué(s)"
        fi
    fi
    
    log_info "Log complet: ${LOG_FILE}"
    log_info "Fin du déploiement - $(date)"
}

# Gestion des signaux pour nettoyer en cas d'interruption
cleanup_on_interrupt() {
    log_error "Déploiement interrompu!"
    # Sauvegarder les fichiers restants si le déploiement était en cours
    if [ -n "${files_to_upload+x}" ] && [ ${#files_to_upload[@]} -gt 0 ]; then
        # Calculer les fichiers non encore uploadés
        local remaining_files=()
        for file in "${files_to_upload[@]}"; do
            local was_uploaded=false
            if [ -n "${uploaded_files+x}" ]; then
                for uploaded in "${uploaded_files[@]}"; do
                    if [ "$file" = "$uploaded" ]; then
                        was_uploaded=true
                        break
                    fi
                done
            fi
            if [ "$was_uploaded" = false ]; then
                remaining_files+=("$file")
            fi
        done
        
        if [ ${#remaining_files[@]} -gt 0 ]; then
            printf '%s\n' "${remaining_files[@]}" > "${FAILED_UPLOADS_FILE}"
            echo -e "${YELLOW}📝 ${#remaining_files[@]} fichier(s) non uploadé(s) sauvegardé(s) dans .failed-uploads${NC}"
            echo -e "${YELLOW}Utilisez './deploy.sh --retry' pour les relancer${NC}"
        fi
        
        # Mettre à jour le commit de référence si des fichiers ont été uploadés
        if [ -n "${uploaded_files+x}" ] && [ ${#uploaded_files[@]} -gt 0 ]; then
            git rev-parse HEAD > "${LAST_DEPLOY_FILE}"
            echo -e "${GREEN}✅ Commit de référence mis à jour malgré l'interruption${NC}"
        fi
    fi
    exit 1
}
trap cleanup_on_interrupt INT TERM

# Exécuter le script principal
main "$@"
