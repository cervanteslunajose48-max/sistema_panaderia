@echo off
echo ==============================================
echo =   LIMPIEZA DE CACHE (MODO DESARROLLO)      =
echo ==============================================
echo.

echo Limpiando toda la cache de Laravel...
cd c:\Webs\PHP\panaderiapasteleria
call php artisan optimize:clear
echo.

echo ==============================================
echo =           ¡CACHE LIMPIADA!                 =
echo =     Ideal para empezar a desarrollar.      =
echo ==============================================
pause
