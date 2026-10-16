@echo off
TITLE Opal Heaven - Reseau Multi-Serveurs Synchronises
cd /d "%~dp0"

echo ======================================================================
echo           OPAL HEAVEN - LANCEMENT MULTI-SERVEURS SYNCHRONISES
echo ======================================================================
echo.
echo [1/2] Lancement du Hub Sanctuaire (Port 19135)...
start "Opal Heaven - HUB SANCTUAIRE (Port 19135)" cmd /k "cd /d "%~dp0server_hub" && start.cmd"

echo.
echo [2/2] Lancement du Faction Overworld (Port 19132)...
start "Opal Heaven - FACTION OVERWORLD (Port 19132)" cmd /k "cd /d "%~dp0server" && start.cmd"

echo.
echo ======================================================================
echo Les deux serveurs sont en cours d'execution et synchronises via SQLite WAL !
echo Hub : 127.0.0.1:19135
echo Faction : 127.0.0.1:19132
echo ======================================================================
