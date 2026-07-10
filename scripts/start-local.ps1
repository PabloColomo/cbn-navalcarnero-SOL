<#
Script de ayuda para arrancar localmente: comprueba `node`/`npm`, ofrece
instalar Node.js vía `winget` si está disponible, y ejecuta `npm install`
y `npm run build` en la raíz del repo.

Uso:
  - Abre PowerShell en la raíz del repo
  - Ejecuta: .\scripts\start-local.ps1

Nota: para instalar con `winget` necesitas ejecutar PowerShell con permisos
de administrador. Si la política de ejecución bloquea el script, ejecuta
`Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser -Force`
#>

function Test-Command($name) {
    return (Get-Command $name -ErrorAction SilentlyContinue) -ne $null
}

Write-Host "Comprobando Node.js / npm en el sistema..."

$hasNode = Test-Command node
$hasNpm = Test-Command npm

if ($hasNode -and $hasNpm) {
    Write-Host "node y npm detectados:" -ForegroundColor Green
    node -v
    npm -v
    Write-Host "Ejecutando 'npm install'..."
    npm install
    if ($LASTEXITCODE -ne 0) { Write-Error "'npm install' falló"; exit $LASTEXITCODE }
    Write-Host "Ejecutando 'npm run build'..."
    npm run build
    exit $LASTEXITCODE
}

Write-Warning "No se ha detectado 'node' o 'npm' en PATH."

if (Test-Command winget) {
    $ans = Read-Host "¿Deseas instalar Node.js LTS automáticamente con winget ahora? (Y/N)"
    if ($ans -match '^[Yy]') {
        Write-Host "Instalando Node.js LTS vía winget (necesita permisos de administrador)..."
        Start-Process -FilePath winget -ArgumentList 'install','--id','OpenJS.NodeJS.LTS','-e','--silent' -Wait -Verb runAs
        Write-Host "Instalación finalizada. Cierra y vuelve a abrir PowerShell y vuelve a ejecutar este script." -ForegroundColor Cyan
        exit 0
    }
}

Write-Host "Opciones:
- Instala Node.js 20+ desde https://nodejs.org y luego vuelve a ejecutar este script.
- Usa Git Bash o WSL (si los tienes) y ejecuta en la raíz del repo: npm install && npm run build
" -ForegroundColor Yellow

Write-Host "Si quieres que intente otra cosa, dime y lo ajusto." -ForegroundColor Gray
