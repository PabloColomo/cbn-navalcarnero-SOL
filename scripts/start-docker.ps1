<#
Arranca Docker Compose y espera a que el servicio de base de datos esté healthy.

Uso: ejecutar desde la raíz del repo:
  .\scripts\start-docker.ps1
#>

Write-Host "Ejecutando: docker compose up -d" -ForegroundColor Cyan
docker compose up -d

Write-Host "Mostrando el estado de los servicios (docker compose ps):" -ForegroundColor Cyan
docker compose ps

Write-Host "Esperando a que el servicio de base de datos esté healthy (buscando contenedor con 'cbn-database')..." -ForegroundColor Cyan

function Get-DatabaseContainerId {
    $ids = docker ps -q --filter "name=cbn-database" 2>$null
    if ($ids) { return $ids[0] }
    return $null
}

$maxWait = 300 # segundos
$interval = 5
$elapsed = 0

while ($elapsed -lt $maxWait) {
    $cid = Get-DatabaseContainerId
    if ($cid) {
        $health = docker inspect --format '{{.State.Health.Status}}' $cid 2>$null
        if ($health -and $health -eq 'healthy') {
            Write-Host "Base de datos healthy (container: $cid)" -ForegroundColor Green
            exit 0
        } else {
            Write-Host "Estado actual: $health. Esperando... ($elapsed/$maxWait s)"
        }
    } else {
        Write-Host "No se encontró contenedor con 'cbn-database' todavía. Esperando... ($elapsed/$maxWait s)"
    }
    Start-Sleep -Seconds $interval
    $elapsed += $interval
}

Write-Warning "Tiempo de espera agotado. Revisa 'docker compose ps' y los logs: docker compose logs cbn-database" 
