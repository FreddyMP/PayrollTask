@echo off
REM Script para verificar que todo está listo para testing
REM Este script verifica archivos clave y estructura

setlocal enabledelayedexpansion
cd /d C:\Users\Freddy\Desktop\proyectos\anti

cls
echo.
echo ========================================
echo   VERIFICACIÓN DE ESTADO - PayrollTask
echo ========================================
echo.

set "all_good=true"

REM Verificar archivos críticos
echo [VERIFICANDO ARCHIVOS CRÍTICOS...]
echo.

if not exist "CONTINUAR_DESDE_AQUI.md" (
    echo [X] FALTA: CONTINUAR_DESDE_AQUI.md
    set "all_good=false"
) else (
    echo [OK] CONTINUAR_DESDE_AQUI.md
)

if not exist "SUMARIO_EJECUTIVO.md" (
    echo [X] FALTA: SUMARIO_EJECUTIVO.md
    set "all_good=false"
) else (
    echo [OK] SUMARIO_EJECUTIVO.md
)

if not exist "SETUP_COMPLETO.bat" (
    echo [X] FALTA: SETUP_COMPLETO.bat
    set "all_good=false"
) else (
    echo [OK] SETUP_COMPLETO.bat
)

if not exist "PRUEBA_RAPIDA.md" (
    echo [X] FALTA: PRUEBA_RAPIDA.md
    set "all_good=false"
) else (
    echo [OK] PRUEBA_RAPIDA.md
)

echo.
echo [VERIFICANDO DIRECTORIOS PRINCIPALES...]
echo.

if not exist "app\Http\Controllers" (
    echo [X] FALTA: app\Http\Controllers
    set "all_good=false"
) else (
    echo [OK] app\Http\Controllers
)

if not exist "resources\views" (
    echo [X] FALTA: resources\views
    set "all_good=false"
) else (
    echo [OK] resources\views
)

if not exist "database\migrations" (
    echo [X] FALTA: database\migrations
    set "all_good=false"
) else (
    echo [OK] database\migrations
)

if not exist "pagina_web" (
    echo [X] FALTA: pagina_web (página web)
    set "all_good=false"
) else (
    echo [OK] pagina_web (página web)
)

echo.
echo [VERIFICANDO ARCHIVOS DE CONFIGURACIÓN...]
echo.

if not exist "artisan" (
    echo [X] FALTA: artisan (archivo no encontrado)
    set "all_good=false"
) else (
    echo [OK] artisan
)

if not exist "composer.json" (
    echo [X] FALTA: composer.json
    set "all_good=false"
) else (
    echo [OK] composer.json
)

if not exist ".env" (
    echo [!] NOTA: .env no encontrado (normal si no está configurado)
) else (
    echo [OK] .env
)

echo.
echo [VERIFICANDO CONTROLADORES CLAVE...]
echo.

if not exist "app\Http\Controllers\CompanyController.php" (
    echo [X] FALTA: CompanyController.php
    set "all_good=false"
) else (
    echo [OK] CompanyController.php
)

if not exist "app\Http\Controllers\DashboardController.php" (
    echo [X] FALTA: DashboardController.php
    set "all_good=false"
) else (
    echo [OK] DashboardController.php
)

if not exist "app\Http\Controllers\AuthController.php" (
    echo [X] FALTA: AuthController.php
    set "all_good=false"
) else (
    echo [OK] AuthController.php
)

echo.
echo [VERIFICANDO VISTAS PRINCIPALES...]
echo.

if not exist "resources\views\dashboard\index.blade.php" (
    echo [X] FALTA: dashboard\index.blade.php
    set "all_good=false"
) else (
    echo [OK] dashboard\index.blade.php
)

if not exist "resources\views\auth\register.blade.php" (
    echo [X] FALTA: auth\register.blade.php
    set "all_good=false"
) else (
    echo [OK] auth\register.blade.php
)

echo.
echo [VERIFICANDO BD...]
echo.

if not exist "database\database.sqlite" (
    echo [!] NOTA: database.sqlite no existe (se creará con migraciones)
) else (
    echo [OK] database.sqlite
)

echo.
echo ========================================
if "%all_good%"=="true" (
    echo   ✅ TODO ESTÁ LISTO PARA TESTING
    echo ========================================
    echo.
    echo Próximos pasos:
    echo.
    echo 1. Ejecutar: SETUP_COMPLETO.bat
    echo    (Para ejecutar migraciones automáticamente)
    echo.
    echo 2. O leer: CONTINUAR_DESDE_AQUI.md
    echo    (Para instrucciones detalladas paso a paso)
    echo.
    echo 3. Luego seguir: PRUEBA_RAPIDA.md
    echo    (Para testing de todas las funcionalidades)
    echo.
) else (
    echo   ⚠️  HAY PROBLEMAS QUE RESOLVER
    echo ========================================
    echo.
    echo Por favor verifica los archivos marcados con [X]
    echo.
)

echo.
echo Documentación disponible:
echo - SUMARIO_EJECUTIVO.md      (Resumen de todo)
echo - CONTINUAR_DESDE_AQUI.md   (Guía paso a paso)
echo - PRUEBA_RAPIDA.md          (Testing detallado)
echo.
echo Para información sobre la página web:
echo - pagina_web/LEEME.md       (Intro)
echo - pagina_web/README.md      (Documentación técnica)
echo - pagina_web/INSTRUCCIONES.md (Cómo usar)
echo.
pause
