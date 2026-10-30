@echo off
setlocal
cd /d "%~dp0"
title WCP360 File Manager - Test local
set "FM_PHP=C:\xampp\php\php.exe"
if exist "%FM_PHP%" goto php_found
set "FM_PHP=D:\xampp\php\php.exe"
if exist "%FM_PHP%" goto php_found
set "FM_PHP=E:\xampp\php\php.exe"
if exist "%FM_PHP%" goto php_found
for %%P in (php.exe) do set "FM_PHP=%%~$PATH:P"
if defined FM_PHP goto php_found
echo PHP introuvable. Indiquez le chemin complet de php.exe, sans guillemets.
echo Exemple : C:\mon-xampp\php\php.exe
set /p "FM_PHP=Chemin : "
:php_found
if not exist "%FM_PHP%" (
    echo ERREUR : php.exe introuvable. Verifiez l'installation XAMPP.
    pause
    exit /b 1
)
echo.
echo WCP360 FILE MANAGER - TEST LOCAL
"%FM_PHP%" standalone\bin\diagnostic.php
if errorlevel 1 goto failed
if exist standalone\var\config.php goto configured
"%FM_PHP%" standalone\bin\setup.php
if errorlevel 1 goto failed
:configured
echo.
echo Ouvrez cette adresse dans votre navigateur :
echo http://127.0.0.1:8088/
echo.
echo Gardez cette fenetre ouverte. Ctrl+C pour arreter.
echo Apache et MySQL ne sont pas necessaires pour ce test.
"%FM_PHP%" -d upload_max_filesize=20M -d post_max_size=22M -d memory_limit=128M -S 127.0.0.1:8088 -t standalone/public standalone/public/index.php
if errorlevel 1 goto failed
exit /b 0
:failed
echo.
echo Le demarrage a echoue. Copiez le message ci-dessus pour le diagnostic.
pause
exit /b 1
