[CmdletBinding()]
param(
    [string]$SiteUrl = '',
    [string]$SiteTitle = 'Club Baloncesto Navalcarnero',
    [string]$AdminUser = 'cbn_admin',
    [string]$AdminEmail = 'admin@cbn.local',
    [string]$AdminPassword = '',
    [switch]$SkipBuild
)

<#
.SYNOPSIS
Prepara una copia local reproducible de CBN Sol en Windows.

.DESCRIPTION
- Copia .env.example cuando .env no existe.
- Instala exactamente las dependencias del lockfile y compila los assets.
- Levanta WordPress y MariaDB con Docker Compose.
- Instala WordPress solo si la base de datos todavía está vacía.
- Activa CBN Theme y crea/configura las páginas esenciales de forma idempotente.

No descarga dumps, no restaura uploads, no cambia de rama y no guarda
credenciales de producción. Si crea WordPress, genera una contraseña local y
la muestra una sola vez al finalizar.
#>

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
Push-Location $repoRoot

function Assert-Command {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Name,
        [Parameter(Mandatory = $true)]
        [string]$InstallHint
    )

    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "No se encontró '$Name'. $InstallHint"
    }
}

function Invoke-WpCli {
    param(
        [Parameter(Mandatory = $true)]
        [string[]]$Arguments,
        [switch]$Capture,
        [switch]$AllowFailure
    )

    $dockerArguments = @(
        'compose',
        '--profile', 'tools',
        'run', '--rm',
        '--user', 'root',
        'wpcli', 'wp'
    ) + $Arguments + @('--allow-root')

    $output = @(& docker @dockerArguments)

    if (-not $Capture -and $output.Count -gt 0) {
        $output | ForEach-Object { Write-Host $_ }
    }

    $exitCode = $LASTEXITCODE

    if (-not $AllowFailure -and $exitCode -ne 0) {
        throw "WP-CLI falló (exit $exitCode): wp $($Arguments -join ' ')"
    }

    return [PSCustomObject]@{
        ExitCode = $exitCode
        Output = $output
    }
}

function Get-NumericOutput {
    param([object[]]$Output)

    $values = @(
        $Output |
            ForEach-Object { ([string]$_).Trim() } |
            Where-Object { $_ -match '^\d+$' }
    )

    if ($values.Count -eq 0) {
        return $null
    }

    return $values[-1]
}

function Ensure-Page {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Title,
        [Parameter(Mandatory = $true)]
        [string]$Slug
    )

    $lookup = Invoke-WpCli -Capture -Arguments @(
        'post', 'list',
        '--post_type=page',
        '--post_status=any',
        "--name=$Slug",
        '--field=ID',
        '--format=ids'
    )
    $pageId = Get-NumericOutput -Output $lookup.Output

    if (-not $pageId) {
        Write-Host "Creando página: $Title" -ForegroundColor Cyan
        $created = Invoke-WpCli -Capture -Arguments @(
            'post', 'create',
            '--post_type=page',
            '--post_status=publish',
            "--post_title=$Title",
            "--post_name=$Slug",
            '--porcelain'
        )
        $pageId = Get-NumericOutput -Output $created.Output
    }

    if (-not $pageId) {
        throw "No se pudo obtener el ID de la página '$Title'."
    }

    return $pageId
}

try {
    Assert-Command -Name 'docker' -InstallHint 'Instala Docker Desktop y vuelve a abrir PowerShell.'

    if (-not $SkipBuild) {
        Assert-Command -Name 'node' -InstallHint 'Instala Node.js 22 LTS o superior.'
        Assert-Command -Name 'npm' -InstallHint 'npm se instala junto con Node.js.'

        $nodeVersion = (& node --version).TrimStart('v')
        $nodeMajor = [int]($nodeVersion.Split('.')[0])

        if ($nodeMajor -lt 22) {
            throw "Se necesita Node.js 22 o superior. Versión detectada: $nodeVersion"
        }
    }

    if (-not (Test-Path -LiteralPath '.env')) {
        Copy-Item -LiteralPath '.env.example' -Destination '.env'
        Write-Host 'Creado .env desde .env.example.' -ForegroundColor Green
    }

    if (-not $SiteUrl) {
        $port = '8080'
        $portLine = Get-Content -LiteralPath '.env' |
            Where-Object { $_ -match '^\s*WORDPRESS_PORT\s*=\s*\d+\s*$' } |
            Select-Object -Last 1

        if ($portLine) {
            $portMatch = [regex]::Match($portLine, '(\d+)\s*$')

            if ($portMatch.Success) {
                $port = $portMatch.Groups[1].Value
            }
        }

        $SiteUrl = "http://localhost:$port"
    }

    if (-not $SkipBuild) {
        Write-Host 'Instalando dependencias bloqueadas por package-lock.json...' -ForegroundColor Cyan
        & npm ci
        if ($LASTEXITCODE -ne 0) {
            throw "npm ci falló (exit $LASTEXITCODE)."
        }

        Write-Host 'Compilando assets del tema...' -ForegroundColor Cyan
        & npm run build
        if ($LASTEXITCODE -ne 0) {
            throw "npm run build falló (exit $LASTEXITCODE)."
        }
    } else {
        Write-Host 'Build omitido. Se usará el manifest vigente o el fallback CSS versionado.' -ForegroundColor Yellow
    }

    Write-Host 'Levantando WordPress y MariaDB...' -ForegroundColor Cyan
    & docker compose up -d
    if ($LASTEXITCODE -ne 0) {
        throw "docker compose up -d falló (exit $LASTEXITCODE)."
    }

    $databaseReady = $false
    $deadline = (Get-Date).AddMinutes(5)

    while ((Get-Date) -lt $deadline) {
        $databaseContainer = ([string](& docker compose ps -q database 2>$null)).Trim()

        if ($databaseContainer) {
            $health = ([string](& docker inspect --format '{{.State.Health.Status}}' $databaseContainer 2>$null)).Trim()

            if ($LASTEXITCODE -eq 0 -and $health -eq 'healthy') {
                $databaseReady = $true
                break
            }
        }

        Start-Sleep -Seconds 2
    }

    if (-not $databaseReady) {
        throw 'MariaDB no alcanzó el estado healthy en cinco minutos. Ejecuta docker compose logs database.'
    }

    $configReady = $false

    for ($attempt = 0; $attempt -lt 60; $attempt++) {
        & docker compose exec -T wordpress test -f /var/www/html/wp-config.php 2>$null

        if ($LASTEXITCODE -eq 0) {
            $configReady = $true
            break
        }

        Start-Sleep -Seconds 2
    }

    if (-not $configReady) {
        throw 'WordPress no creó wp-config.php. Ejecuta docker compose logs wordpress.'
    }

    $installedCheck = Invoke-WpCli -AllowFailure -Arguments @('core', 'is-installed')
    $createdLocalAdmin = $false

    if ($installedCheck.ExitCode -ne 0) {
        if (-not $AdminPassword) {
            $AdminPassword = 'CBN-local-' + [Guid]::NewGuid().ToString('N').Substring(0, 18) + '!'
        }

        Write-Host 'Instalando WordPress local...' -ForegroundColor Cyan
        Invoke-WpCli -Arguments @(
            'core', 'install',
            "--url=$SiteUrl",
            "--title=$SiteTitle",
            "--admin_user=$AdminUser",
            "--admin_password=$AdminPassword",
            "--admin_email=$AdminEmail",
            '--skip-email'
        ) | Out-Null
        $createdLocalAdmin = $true
    }

    if ($createdLocalAdmin) {
        $samplePostLookup = Invoke-WpCli -Capture -Arguments @(
            'post', 'list',
            '--post_type=post',
            '--post_status=any',
            '--name=hello-world',
            '--field=ID',
            '--format=ids'
        )
        $samplePostId = Get-NumericOutput -Output $samplePostLookup.Output

        if ($samplePostId) {
            Invoke-WpCli -Arguments @(
                'post', 'update',
                $samplePostId,
                '--post_status=draft'
            ) | Out-Null
        }
    }

    Write-Host 'Activando CBN Theme y preparando la estructura pública...' -ForegroundColor Cyan
    Invoke-WpCli -Arguments @('theme', 'activate', 'cbn-theme') | Out-Null
    Invoke-WpCli -Arguments @('language', 'core', 'install', 'es_ES', '--activate') | Out-Null

    $homePageId = Ensure-Page -Title 'Inicio' -Slug 'inicio'
    $newsPageId = Ensure-Page -Title 'Noticias' -Slug 'noticias'
    Ensure-Page -Title 'El Club' -Slug 'el-club' | Out-Null
    Ensure-Page -Title 'Contacto' -Slug 'contacto' | Out-Null
    Ensure-Page -Title 'Inscripción' -Slug 'inscripcion' | Out-Null
    Ensure-Page -Title 'Tienda' -Slug 'tienda' | Out-Null
    Ensure-Page -Title 'Documentación' -Slug 'documentacion' | Out-Null
    Ensure-Page -Title 'Privacidad' -Slug 'privacidad' | Out-Null
    Ensure-Page -Title 'Aviso legal' -Slug 'aviso-legal' | Out-Null

    $optionUpdates = @(
        @('blogname', $SiteTitle),
        @('home', $SiteUrl),
        @('siteurl', $SiteUrl),
        @('show_on_front', 'page'),
        @('page_on_front', $homePageId),
        @('page_for_posts', $newsPageId),
        @('users_can_register', '0'),
        @('blog_public', '0'),
        @('default_comment_status', 'closed'),
        @('timezone_string', 'Europe/Madrid')
    )

    foreach ($option in $optionUpdates) {
        Invoke-WpCli -Arguments @('option', 'update', $option[0], $option[1]) | Out-Null
    }

    Invoke-WpCli -Arguments @('rewrite', 'structure', '/%postname%/') | Out-Null
    Invoke-WpCli -Arguments @('rewrite', 'flush') | Out-Null

    Write-Host ''
    Write-Host 'CBN Sol está listo.' -ForegroundColor Green
    Write-Host "Web:   $SiteUrl"
    Write-Host "Admin: $SiteUrl/wp-login.php"

    if ($createdLocalAdmin) {
        Write-Host ''
        Write-Host 'Credenciales creadas solo para este entorno local:' -ForegroundColor Yellow
        Write-Host "Usuario:    $AdminUser"
        Write-Host "Contraseña: $AdminPassword"
        Write-Host 'Guárdalas ahora: la contraseña no se escribe en el repositorio.' -ForegroundColor Yellow
    } else {
        Write-Host 'WordPress ya estaba instalado; se conservan sus usuarios y contenidos.' -ForegroundColor Gray
    }

    Write-Host 'Si el navegador estaba abierto, recarga con Ctrl+F5.' -ForegroundColor Cyan
} finally {
    Pop-Location
}
