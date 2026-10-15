@echo off
echo ========================================================
echo   OPAL HEAVEN - ACTIVATION DU LOOPBACK MINECRAFT BEDROCK
echo ========================================================
echo.
echo Autorisation de la connexion locale (127.0.0.1) pour Minecraft Windows...
CheckNetIsolation.exe LoopbackExempt -a -n="Microsoft.MinecraftUWP_8wekyb3d8bbwe"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [SUCCES] Le Loopback est active ! Tu peux maintenant te connecter
    echo          a ton serveur local depuis Minecraft Bedrock (IP: 127.0.0.1 Port: 19132).
) else (
    echo.
    echo [ERREUR] Echec de l'activation. Assure-toi d'executer ce fichier en Administrateur.
)
echo.
pause
