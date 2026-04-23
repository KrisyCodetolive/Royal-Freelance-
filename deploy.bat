@echo off
setlocal enabledelayedexpansion

REM Script de déploiement CLAP pour Windows
REM Usage: deploy.bat [options]

set "SCRIPT_DIR=%~dp0"
set "CONFIG_FILE=%SCRIPT_DIR%ftp-config.bat"
set "LOG_DIR=%SCRIPT_DIR%deploy-logs"
set "TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
set "TIMESTAMP=%TIMESTAMP: =0%"
set "LOG_FILE=%LOG_DIR%\deploy_%TIMESTAMP%.log"
set "LAST_DEPLOY_FILE=%SCRIPT_DIR%.last-deploy"
set "FORCE_UPLOAD=false"
set "DRY_RUN=false"
set "VERBOSE=false"

REM Créer le répertoire de logs
if not exist "%LOG_DIR%" mkdir "%LOG_DIR%"

REM Couleurs (codes ANSI pour Windows 10+)
set "RED=[31m"
set "GREEN=[32m"
set "YELLOW=[33m"
set "BLUE=[34m"
set "PURPLE=[35m"
set "CYAN=[36m"
set "NC=[0m"

:show_help
echo.
echo %PURPLE%╔══════════════════════════════════════════════════════════════╗%NC%
echo %PURPLE%║                  🚀 CLAP DEPLOY SCRIPT 🚀                   ║%NC%
echo %PURPLE%║              Déploiement Continu avec FTP                   ║%NC%
echo %PURPLE%╚══════════════════════════════════════════════════════════════╝%NC%
echo.
echo Usage: deploy.bat [OPTIONS]
echo.
echo OPTIONS:
echo     /h, /help          Afficher cette aide
echo     /f, /force         Forcer l'upload de tous les fichiers
echo     /d, /dry-run       Mode simulation (ne fait pas l'upload)
echo     /v, /verbose       Mode verbeux
echo.
echo EXEMPLES:
echo     deploy.bat                    # Déploiement normal
echo     deploy.bat /dry-run          # Simulation
echo     deploy.bat /force            # Upload complet
echo.
echo CONFIGURATION:
echo     Créez un fichier 'ftp-config.bat' avec vos paramètres FTP.
echo.
goto :eof

:log_info
echo %BLUE%ℹ️  %~1%NC%
echo [%date% %time%] [INFO] %~1 >> "%LOG_FILE%"
goto :eof

:log_success
echo %GREEN%✅ %~1%NC%
echo [%date% %time%] [SUCCESS] %~1 >> "%LOG_FILE%"
goto :eof

:log_warning
echo %YELLOW%⚠️  %~1%NC%
echo [%date% %time%] [WARNING] %~1 >> "%LOG_FILE%"
goto :eof

:log_error
echo %RED%❌ %~1%NC%
echo [%date% %time%] [ERROR] %~1 >> "%LOG_FILE%"
goto :eof

:parse_args
if "%~1"=="" goto :end_parse
if /i "%~1"=="/h" goto :show_help
if /i "%~1"=="/help" goto :show_help
if /i "%~1"=="/f" set "FORCE_UPLOAD=true"
if /i "%~1"=="/force" set "FORCE_UPLOAD=true"
if /i "%~1"=="/d" set "DRY_RUN=true"
if /i "%~1"=="/dry-run" set "DRY_RUN=true"
if /i "%~1"=="/v" set "VERBOSE=true"
if /i "%~1"=="/verbose" set "VERBOSE=true"
shift
goto :parse_args
:end_parse
goto :eof

:check_git
if not exist ".git" (
    call :log_error "Ce répertoire n'est pas un dépôt Git!"
    exit /b 1
)
goto :eof

:load_config
if not exist "%CONFIG_FILE%" (
    call :log_error "Fichier de configuration FTP introuvable: %CONFIG_FILE%"
    call :log_info "Créez un fichier 'ftp-config.bat' avec vos paramètres FTP."
    echo.
    echo Exemple de contenu pour ftp-config.bat:
    echo set "FTP_HOST=ftp.votre-domaine.com"
    echo set "FTP_USER=votre_utilisateur"
    echo set "FTP_PASS=votre_mot_de_passe"
    echo set "FTP_REMOTE_DIR=/public_html"
    echo set "FTP_PORT=21"
    exit /b 1
)

call "%CONFIG_FILE%"

if "%FTP_HOST%"=="" (
    call :log_error "Configuration FTP incomplète! Vérifiez FTP_HOST."
    exit /b 1
)
if "%FTP_USER%"=="" (
    call :log_error "Configuration FTP incomplète! Vérifiez FTP_USER."
    exit /b 1
)
if "%FTP_PASS%"=="" (
    call :log_error "Configuration FTP incomplète! Vérifiez FTP_PASS."
    exit /b 1
)
goto :eof

:get_modified_files
set "FILES_LIST=%TEMP%\clap_files_%TIMESTAMP%.txt"

if "%FORCE_UPLOAD%"=="true" (
    call :log_info "Mode force activé - récupération de tous les fichiers..."
    dir /b /s /a-d *.* | findstr /v /i "\.git\\" | findstr /v /i "node_modules\\" | findstr /v /i "vendor\\" > "%FILES_LIST%"
) else (
    call :log_info "Détection des fichiers modifiés via Git..."
    
    REM Récupérer les fichiers modifiés depuis le dernier commit
    if exist "%LAST_DEPLOY_FILE%" (
        set /p LAST_COMMIT=<"%LAST_DEPLOY_FILE%"
        git diff --name-only !LAST_COMMIT! > "%FILES_LIST%" 2>nul
    ) else (
        REM Premier déploiement
        git ls-files > "%FILES_LIST%" 2>nul
    )
)

REM Compter les fichiers
set "FILE_COUNT=0"
for /f %%i in ('type "%FILES_LIST%" ^| find /c /v ""') do set "FILE_COUNT=%%i"

goto :eof

:upload_files
call :log_info "Début de l'upload de %FILE_COUNT% fichiers..."

set "SUCCESS_COUNT=0"
set "ERROR_COUNT=0"

REM Créer le script FTP
set "FTP_SCRIPT=%TEMP%\clap_ftp_%TIMESTAMP%.txt"

echo open %FTP_HOST% %FTP_PORT% > "%FTP_SCRIPT%"
echo %FTP_USER% >> "%FTP_SCRIPT%"
echo %FTP_PASS% >> "%FTP_SCRIPT%"
echo binary >> "%FTP_SCRIPT%"

REM Ajouter les commandes d'upload pour chaque fichier
for /f "delims=" %%f in ('type "%FILES_LIST%"') do (
    if "%DRY_RUN%"=="true" (
        call :log_info "[DRY-RUN] Upload: %%f"
    ) else (
        set "REMOTE_FILE=%FTP_REMOTE_DIR%/%%f"
        set "REMOTE_FILE=!REMOTE_FILE:\=/!"
        echo put "%%f" "!REMOTE_FILE!" >> "%FTP_SCRIPT%"
    )
)

echo quit >> "%FTP_SCRIPT%"

if "%DRY_RUN%"=="false" (
    REM Exécuter le script FTP
    ftp -n -s:"%FTP_SCRIPT%" > "%LOG_FILE%.ftp" 2>&1
    
    if !errorlevel! equ 0 (
        set "SUCCESS_COUNT=%FILE_COUNT%"
        call :log_success "Upload terminé avec succès!"
    ) else (
        set "ERROR_COUNT=%FILE_COUNT%"
        call :log_error "Erreurs lors de l'upload. Consultez %LOG_FILE%.ftp"
    )
    
    REM Nettoyer
    del "%FTP_SCRIPT%" 2>nul
)

del "%FILES_LIST%" 2>nul
goto :eof

:main
call :parse_args %*

echo.
echo %PURPLE%╔══════════════════════════════════════════════════════════════╗%NC%
echo %PURPLE%║                  🚀 CLAP DEPLOY SCRIPT 🚀                   ║%NC%
echo %PURPLE%║              Déploiement Continu avec FTP                   ║%NC%
echo %PURPLE%╚══════════════════════════════════════════════════════════════╝%NC%
echo.

call :log_info "Début du déploiement - %date% %time%"

REM Vérifications
call :check_git
if errorlevel 1 exit /b 1

call :load_config
if errorlevel 1 exit /b 1

call :log_info "Serveur FTP: %FTP_HOST%"
call :log_info "Répertoire distant: %FTP_REMOTE_DIR%"

REM Récupérer les fichiers à uploader
call :get_modified_files

if "%FILE_COUNT%"=="0" (
    call :log_info "Aucun fichier à déployer!"
    goto :end
)

call :log_info "Fichiers à déployer: %FILE_COUNT%"

REM Confirmation
if "%DRY_RUN%"=="false" (
    echo.
    set /p "CONFIRM=Voulez-vous continuer le déploiement? (y/N): "
    if /i not "!CONFIRM!"=="y" (
        call :log_info "Déploiement annulé par l'utilisateur"
        goto :end
    )
)

REM Upload
call :upload_files

REM Sauvegarder le commit actuel
if "%DRY_RUN%"=="false" if "%ERROR_COUNT%"=="0" (
    git rev-parse HEAD > "%LAST_DEPLOY_FILE%" 2>nul
)

echo.
call :log_success "Fichiers uploadés: %SUCCESS_COUNT%"
if not "%ERROR_COUNT%"=="0" (
    call :log_error "Fichiers en erreur: %ERROR_COUNT%"
)

call :log_info "Log complet: %LOG_FILE%"
call :log_info "Fin du déploiement - %date% %time%"

:end
pause
goto :eof

REM Point d'entrée principal
call :main %*
