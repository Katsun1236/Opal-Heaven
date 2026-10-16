@echo off
TITLE Opal Heaven - Serveur Hub Sanctuaire (Port 19135)
cd /d "%~dp0"

echo ======================================================================
echo           OPAL HEAVEN - SERVEUR HUB SANCTUAIRE (Port 19135)
echo ======================================================================
echo.

if not exist bin\php.exe (
    echo [ERREUR] Le binaire PHP n'a pas ete trouve dans server_hub\bin\php.exe !
    pause
    exit /b 1
)

if not exist pmmp_src\src\PocketMine.php (
    echo [ERREUR] pmmp_src\src\PocketMine.php est introuvable !
    pause
    exit /b 1
)

echo Demarrage du Hub Céleste sur le port 19135 UDP...
echo.

:loop
bin\php.exe pmmp_src\src\PocketMine.php --no-wizard %*

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [AVERTISSEMENT] Le serveur Hub s'est arrete avec le code %ERRORLEVEL%.
)

echo.
echo Le Hub a ete arrete. Redemarrage automatique dans 2 secondes...
timeout /t 2 >nul
goto loop
