<#
Automatiza el workflow para dejar la máquina igual que la del compañero:
- opcion de usar la rama `feat/fase1-visual-hardening` o aplicar PR #22
- ejecutar `npm install` + `npm run build`
- levantar Docker Compose y esperar la base de datos
- importar el dump SQL (si está presente)
- restaurar uploads desde ZIP (si se proporciona)

Uso: desde la raíz del repo en PowerShell:
  Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser -Force
  .\scripts\bootstrap-local.ps1
#>

function Run-ExitOnFail($cmd, [string]$display = $null) {
    if (-not $display) { $display = $cmd }
    Write-Host "-> Ejecutando: $display" -ForegroundColor Cyan
    & cmd /c $cmd
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Comando falló: $display (exit $LASTEXITCODE)"
        exit $LASTEXITCODE
    }
}

if (-not (Test-Path .git)) {
    Write-Error "Parece que no estás en la raíz de un repo git (no existe .git). Sitúate en la raíz y vuelve a ejecutar." ; exit 1
}

Write-Host "Sincronizando con origin..." -ForegroundColor Cyan
Run-ExitOnFail "git fetch origin" "git fetch origin"

$useBranch = Read-Host "¿Usar la rama feat/fase1-visual-hardening si existe? (Y/N)"
if ($useBranch -match '^[Yy]') {
    Write-Host "Intentando hacer checkout de feat/fase1-visual-hardening..." -ForegroundColor Cyan
    # Intentar checkout local, si falla traer desde origin
    $rc = & git checkout feat/fase1-visual-hardening 2>$null
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Rama no existe localmente. Intentando traer desde origin..." -ForegroundColor Yellow
        Run-ExitOnFail "git fetch origin feat/fase1-visual-hardening:feat/fase1-visual-hardening" "git fetch origin feat/fase1-visual-hardening:feat/fase1-visual-hardening"
        Run-ExitOnFail "git checkout feat/fase1-visual-hardening" "git checkout feat/fase1-visual-hardening"
    } else {
        Write-Host "Checked out feat/fase1-visual-hardening" -ForegroundColor Green
    }
} else {
    $usePR = Read-Host "¿Aplicar PR #22 (traer pull/22/head)? (Y/N)"
    if ($usePR -match '^[Yy]') {
        Write-Host "Traer PR #22 a la rama local pr-22..." -ForegroundColor Cyan
        Run-ExitOnFail "git fetch origin pull/22/head:pr-22" "git fetch origin pull/22/head:pr-22"
        Run-ExitOnFail "git checkout pr-22" "git checkout pr-22"
    } else {
        Write-Host "Continuando en la rama actual." -ForegroundColor Gray
    }
}

Write-Host "Ejecutando build de frontend (node/npm) usando scripts/start-local.ps1" -ForegroundColor Cyan
& .\scripts\start-local.ps1

Write-Host "Arrancando Docker Compose y esperando la BD" -ForegroundColor Cyan
& .\scripts\start-docker.ps1

$sqlPresent = Test-Path .\cbn-db-export-2026-07-09.sql
if ($sqlPresent) {
    $doImport = Read-Host "Se ha encontrado cbn-db-export-2026-07-09.sql. ¿Importar la BD ahora? (Y/N)"
    if ($doImport -match '^[Yy]') {
        & .\scripts\import-db.ps1
    } else { Write-Host "Importación omitida." -ForegroundColor Yellow }
} else {
    Write-Host "No se encontró cbn-db-export-2026-07-09.sql en la raíz. Copia el dump si quieres importarlo." -ForegroundColor Yellow
}

$defaultZip = ".\uploads-2026-07-09.zip"
$zipPath = Read-Host "Ruta del ZIP de uploads (dejar vacío para $defaultZip)"
if ([string]::IsNullOrWhiteSpace($zipPath)) { $zipPath = $defaultZip }
if (Test-Path $zipPath) {
    $doRestore = Read-Host "¿Deseas extraer $zipPath en wp-content/uploads/? (Y/N)"
    if ($doRestore -match '^[Yy]') {
        & .\scripts\restore-uploads.ps1 -ZipPath $zipPath
    } else { Write-Host "Restauración de uploads omitida." -ForegroundColor Yellow }
} else {
    Write-Host "No se encontró el ZIP de uploads: $zipPath" -ForegroundColor Yellow
}

Write-Host "Proceso completado. Abre http://localhost:8080 y fuerza recarga (Ctrl+F5) si es necesario." -ForegroundColor Green
