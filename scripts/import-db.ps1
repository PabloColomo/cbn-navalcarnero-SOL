<#
Importa el dump SQL a la instalación de WP en Docker usando el servicio `wpcli` del compose.

Coloca `cbn-db-export-2026-07-09.sql` en la raíz del repo y ejecuta:
  .\scripts\import-db.ps1

Este script monta la carpeta del repo en /workspace dentro del contenedor.
#>

if (-not (Test-Path -Path .\cbn-db-export-2026-07-09.sql)) {
    Write-Error "No se encontró cbn-db-export-2026-07-09.sql en la raíz del proyecto. Copia el archivo y vuelve a ejecutar."
    exit 1
}

Write-Host "Importando cbn-db-export-2026-07-09.sql mediante wpcli..." -ForegroundColor Cyan

docker compose --profile tools run --rm --user root -v "${PWD}:/workspace" -w /workspace wpcli wp db import --allow-root cbn-db-export-2026-07-09.sql

if ($LASTEXITCODE -eq 0) {
    Write-Host "Importación completada." -ForegroundColor Green
} else {
    Write-Error "La importación falló. Revisa la salida anterior para detalles." 
}
