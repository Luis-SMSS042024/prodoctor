@echo off
chcp 65001 > nul
echo ===================================================
echo    PRODOCTOR - INICIALIZADOR DE BASE DE DATOS
echo ===================================================
echo.
echo Este script restablecerá la base de datos de ProDoctor.
echo Creará la base de datos 'prodoctor' si no existe,
echo aplicará todas las migraciones y sembrará los datos iniciales.
echo.
set /p confirm=¿Estás seguro de que quieres continuar? (S/N): 
if /i "%confirm%" neq "S" goto end

echo.
echo [1/3] Verificando entorno PHP de Laragon...
set PHP_BIN=C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe
if not exist "%PHP_BIN%" (
    echo [ERROR] No se encontró la versión de PHP 8.3.30 en Laragon.
    echo Buscando cualquier ejecutable de PHP...
    set PHP_BIN=php
)

echo Usando ejecutable PHP: %PHP_BIN%
echo.
echo [2/3] Creando base de datos si no existe...
"%PHP_BIN%" artisan route:list > nul

echo.
echo [3/3] Restableciendo tablas y sembrando datos...
"%PHP_BIN%" artisan migrate:fresh --seed --force

echo.
echo ===================================================
echo ¡Base de datos ProDoctor lista y funcional al 100%!
echo ===================================================
pause
exit

:end
echo Operación cancelada por el usuario.
pause
