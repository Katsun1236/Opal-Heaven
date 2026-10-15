@echo off
TITLE Opal Heaven - Serveur Minecraft Bedrock
cd /d "%~dp0"

echo ======================================================================
echo           OPAL HEAVEN - SERVEUR MINECRAFT BEDROCK
echo ======================================================================
echo.

:: 1. Tuer les anciens processus PHP qui verrouillent le dossier ou le port UDP
taskkill /F /IM php.exe >nul 2>&1

:: 2. Nettoyer les caches phar temporaires
rmdir /s /q "%TEMP%\PocketMine-MP-phar-cache.0" >nul 2>&1

if not exist bin\php.exe (
    echo [ERREUR] Le binaire PHP n'a pas ete trouve dans server\bin\php.exe !
    pause
    exit /b 1
)

if not exist pmmp_src\src\PocketMine.php (
    echo [ERREUR] pmmp_src\src\PocketMine.php est introuvable !
    pause
    exit /b 1
)

echo Demarrage du serveur PocketMine-MP 5 (Patch Multi-Protocole Actif) sur le port 19132 UDP...
echo.

:loop
bin\php.exe pmmp_src\src\PocketMine.php --no-wizard %*

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [AVERTISSEMENT] Le serveur s'est arrete avec le code %ERRORLEVEL%.
)

echo.
echo Le serveur a ete arrete. Redemarrage automatique dans 1 seconde...
timeout /t 1 >nul
goto loop

:end
echo Arret termine.
