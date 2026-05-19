@echo off
echo ==============================================
echo =       OPTIMIZACION DE LA APLICACION        =
echo ==============================================
echo.

echo 1. Compilando assets del frontend (Vite)...
call npm run build
echo.

echo 2. Limpiando y regenerando cache de Laravel...
cd c:\Webs\PHP\panaderiapasteleria
call php artisan optimize:clear
call php artisan view:cache
echo.

echo ==============================================
echo =         ¡OPTIMIZACION COMPLETADA!          =
echo = El sistema ahora cargara mucho mas rapido. =
echo ==============================================
pause
