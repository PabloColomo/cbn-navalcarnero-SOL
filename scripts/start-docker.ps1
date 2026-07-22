[CmdletBinding()]
param()

<#
Arranca Docker Compose y espera a que el servicio database esté healthy.
No depende del nombre del proyecto definido en .env.
#>

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path

Push-Location $repoRoot

try {
    if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
        throw 'Instala Docker Desktop y vuelve a abrir PowerShell.'
    }

    & docker compose up -d
    if ($LASTEXITCODE -ne 0) {
        throw "docker compose up -d falló (exit $LASTEXITCODE)."
    }

    $ready = $false
    $deadline = (Get-Date).AddMinutes(5)

    while ((Get-Date) -lt $deadline) {
        $containerId = ([string](& docker compose ps -q database 2>$null)).Trim()

        if ($containerId) {
            $health = ([string](& docker inspect --format '{{.State.Health.Status}}' $containerId 2>$null)).Trim()

            if ($LASTEXITCODE -eq 0 -and $health -eq 'healthy') {
                $ready = $true
                break
            }
        }

        Start-Sleep -Seconds 2
    }

    if (-not $ready) {
        throw 'MariaDB no alcanzó el estado healthy. Ejecuta docker compose logs database.'
    }

    & docker compose ps
    Write-Host 'Docker está listo.' -ForegroundColor Green
} finally {
    Pop-Location
}
