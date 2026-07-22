[CmdletBinding()]
param()

<#
Instala las dependencias exactas del lockfile y compila los assets.
Para preparar también Docker y WordPress usa bootstrap-local.ps1.
#>

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path

Push-Location $repoRoot

try {
    if (-not (Get-Command node -ErrorAction SilentlyContinue) -or -not (Get-Command npm -ErrorAction SilentlyContinue)) {
        throw 'Instala Node.js 22 LTS o superior y vuelve a abrir PowerShell.'
    }

    $nodeVersion = (& node --version).TrimStart('v')
    $nodeMajor = [int]($nodeVersion.Split('.')[0])

    if ($nodeMajor -lt 22) {
        throw "Se necesita Node.js 22 o superior. Versión detectada: $nodeVersion"
    }

    Write-Host 'Ejecutando npm ci...' -ForegroundColor Cyan
    & npm ci
    if ($LASTEXITCODE -ne 0) {
        throw "npm ci falló (exit $LASTEXITCODE)."
    }

    Write-Host 'Ejecutando npm run build...' -ForegroundColor Cyan
    & npm run build
    if ($LASTEXITCODE -ne 0) {
        throw "npm run build falló (exit $LASTEXITCODE)."
    }

    Write-Host 'Assets compilados correctamente.' -ForegroundColor Green
} finally {
    Pop-Location
}
