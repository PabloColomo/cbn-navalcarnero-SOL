<#
Restaura el ZIP de uploads dentro de wp-content/uploads/

Uso:
  .\scripts\restore-uploads.ps1 -ZipPath .\uploads-2026-07-09.zip

Si no se especifica ZipPath el script buscará en la raíz `uploads-2026-07-09.zip`.
#>

param(
    [string]$ZipPath = "./uploads-2026-07-09.zip"
)

if (-not (Test-Path $ZipPath)) {
    Write-Error "No se encontró el ZIP: $ZipPath"
    exit 1
}

$dest = Join-Path -Path (Get-Location) -ChildPath "wp-content/uploads"
if (-not (Test-Path $dest)) { New-Item -ItemType Directory -Path $dest -Force | Out-Null }

Write-Host "Extrayendo $ZipPath → $dest" -ForegroundColor Cyan
Expand-Archive -Path $ZipPath -DestinationPath $dest -Force

Write-Host "Restauración completada." -ForegroundColor Green
