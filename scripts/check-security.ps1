<#
.SYNOPSIS
    Comprobaciones de seguridad externas sobre una instancia de CBN Sol.

.DESCRIPTION
    Reproduce lo que ve un revisor externo (por ejemplo, el equipo de riesgos
    del banco antes de dar de alta el TPV virtual) sin tocar la base de datos
    ni el sistema de archivos. Solo hace peticiones HTTP de lectura.

    Pensado para ejecutarse contra el entorno local levantado con Docker y,
    más adelante, contra el dominio real antes de pedir la revisión.

.PARAMETER BaseUrl
    URL raíz del sitio. Por defecto http://localhost:8084

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\scripts\check-security.ps1
    powershell -ExecutionPolicy Bypass -File .\scripts\check-security.ps1 -BaseUrl https://cbnavalcarnero.es
#>

[CmdletBinding()]
param(
    [string]$BaseUrl = 'http://localhost:8084'
)

$ErrorActionPreference = 'Stop'
$BaseUrl = $BaseUrl.TrimEnd('/')
$script:Pass = 0
$script:Fail = 0
$script:Warn = 0

function Write-Result {
    param([string]$Status, [string]$Check, [string]$Detail)

    switch ($Status) {
        'OK'   { $color = 'Green';  $script:Pass++ }
        'FALLO'{ $color = 'Red';    $script:Fail++ }
        default{ $color = 'Yellow'; $script:Warn++ }
    }

    Write-Host ('[{0,-5}] {1}' -f $Status, $Check) -ForegroundColor $color
    if ($Detail) { Write-Host ('         {0}' -f $Detail) -ForegroundColor DarkGray }
}

function Invoke-Probe {
    param([string]$Path, [string]$Method = 'GET')

    # Compatible con Windows PowerShell 5.1: -SkipHttpErrorCheck no existe ahi
    # (es de PowerShell 7+) y tanto los 4xx/5xx como el limite de redirecciones
    # se comunican como error. Se captura el error y se reconstruye un objeto
    # equivalente (StatusCode, Headers, Content) desde la respuesta subyacente.
    $probeError = $null
    $response = $null

    try {
        $response = Invoke-WebRequest -Uri "$BaseUrl$Path" -Method $Method -MaximumRedirection 0 `
            -TimeoutSec 20 -UseBasicParsing -ErrorAction SilentlyContinue -ErrorVariable probeError
    } catch {
        $probeError = @($_)
    }

    if ($response) {
        return $response
    }

    if (-not $probeError -or -not $probeError[0].Exception -or -not $probeError[0].Exception.Response) {
        return $null
    }

    $webResponse = $probeError[0].Exception.Response
    $content = ''

    try {
        $stream = $webResponse.GetResponseStream()

        if ($stream) {
            $reader = New-Object System.IO.StreamReader($stream)
            $content = $reader.ReadToEnd()
            $reader.Close()
        }
    } catch {}

    $headers = @{}

    foreach ($key in $webResponse.Headers.AllKeys) {
        $headers[$key] = $webResponse.Headers[$key]
    }

    return [PSCustomObject]@{
        StatusCode = [int]$webResponse.StatusCode
        Headers    = $headers
        Content    = $content
    }
}

Write-Host ''
Write-Host "Revisando $BaseUrl" -ForegroundColor Cyan
Write-Host ('-' * 60)

# --- 1. Cabeceras de seguridad -------------------------------------------
# $homeResponse y no $home: HOME es una variable automatica de solo lectura
# en PowerShell y asignarla aborta el script.
$homeResponse = Invoke-Probe '/'

if ($null -eq $homeResponse) {
    Write-Result 'FALLO' 'El sitio responde' "No se pudo conectar con $BaseUrl. ¿Está Docker levantado?"
    exit 1
}

Write-Result 'OK' 'El sitio responde' "HTTP $($homeResponse.StatusCode)"

$expected = @{
    'X-Content-Type-Options' = 'nosniff'
    'Referrer-Policy'        = 'strict-origin-when-cross-origin'
    'X-Frame-Options'        = 'SAMEORIGIN'
    'Permissions-Policy'     = $null
}

foreach ($header in $expected.Keys) {
    $value = $homeResponse.Headers[$header]
    if ($value) {
        Write-Result 'OK' "Cabecera $header" ($value -join ', ')
    } else {
        Write-Result 'FALLO' "Cabecera $header" 'Ausente. Revisa mu-plugins/cbn-core/inc/hardening.php'
    }
}

if ($BaseUrl.StartsWith('https://')) {
    if ($homeResponse.Headers['Strict-Transport-Security']) {
        Write-Result 'OK' 'HSTS' ($homeResponse.Headers['Strict-Transport-Security'] -join ', ')
    } else {
        Write-Result 'FALLO' 'HSTS' 'Falta Strict-Transport-Security. Se configura en el servidor web, no en PHP.'
    }
} else {
    Write-Result 'AVISO' 'HSTS' 'Omitido: la comprobacion solo aplica sobre HTTPS.'
}

if ($homeResponse.Headers['X-Pingback']) {
    Write-Result 'FALLO' 'Cabecera X-Pingback' 'Presente: delata el endpoint XML-RPC.'
} else {
    Write-Result 'OK' 'Cabecera X-Pingback' 'Ausente.'
}

# --- 2. Divulgacion de version -------------------------------------------
if ($homeResponse.Content -match '<meta name="generator"[^>]*WordPress') {
    Write-Result 'FALLO' 'Version de WordPress oculta' 'La etiqueta generator sigue publicando la version exacta.'
} else {
    Write-Result 'OK' 'Version de WordPress oculta' 'Sin etiqueta generator.'
}

foreach ($h in @('Server', 'X-Powered-By')) {
    $value = $homeResponse.Headers[$h]
    if ($value -and ($value -join '') -match '\d+\.\d+') {
        Write-Result 'AVISO' "Cabecera $h" "Publica version: $($value -join ', '). Ocultala en el servidor web."
    } else {
        Write-Result 'OK' "Cabecera $h" 'Sin numero de version.'
    }
}

# --- 3. Superficies que deben estar cerradas -----------------------------
$closed = @(
    @{ Path = '/xmlrpc.php';                Name = 'XML-RPC cerrado';                  Ok = @(403, 404, 405) }
    @{ Path = '/wp-json/wp/v2/users';       Name = 'Enumeracion REST de usuarios';     Ok = @(401, 403, 404) }
    @{ Path = '/?author=1';                 Name = 'Enumeracion por ?author';          Ok = @(301, 302, 404) }
    @{ Path = '/wp-json/wp/v2/comments';    Name = 'Endpoint REST de comentarios';     Ok = @(401, 403, 404) }
    @{ Path = '/wp-content/debug.log';      Name = 'Log de depuracion no servido';     Ok = @(403, 404) }
    @{ Path = '/wp-config.php.bak';         Name = 'Copias de wp-config no servidas';  Ok = @(403, 404) }
    @{ Path = '/readme.html';               Name = 'readme.html de WordPress';         Ok = @(403, 404) }
    @{ Path = '/wp-content/uploads/';       Name = 'Listado de directorio en uploads'; Ok = @(403, 404) }
)

foreach ($probe in $closed) {
    $response = Invoke-Probe $probe.Path
    if ($null -eq $response) {
        Write-Result 'AVISO' $probe.Name 'Sin respuesta.'
        continue
    }

    if ($probe.Ok -contains $response.StatusCode) {
        Write-Result 'OK' $probe.Name "HTTP $($response.StatusCode)"
    } else {
        Write-Result 'FALLO' $probe.Name "HTTP $($response.StatusCode) - accesible. Ver docs/despliegue-produccion.md"
    }
}

# --- 4. Paginas legales exigidas para el alta del TPV --------------------
Write-Host ''
Write-Host 'Paginas legales (requisito de Redsys / banco)' -ForegroundColor Cyan

$legal = @(
    @{ Path = '/aviso-legal/';  Name = 'Aviso legal' }
    @{ Path = '/privacidad/';   Name = 'Politica de privacidad' }
    @{ Path = '/cookies/';      Name = 'Politica de cookies' }
    @{ Path = '/condiciones-de-compra/'; Name = 'Condiciones de compra' }
    @{ Path = '/devoluciones/'; Name = 'Politica de devoluciones' }
)

foreach ($page in $legal) {
    $response = Invoke-Probe $page.Path

    if ($null -eq $response -or $response.StatusCode -ne 200) {
        Write-Result 'FALLO' $page.Name 'No existe o no responde 200.'
        continue
    }

    # Un <main> con muy poco texto es una pagina creada pero vacia. Se mide
    # solo el contenido de <main>: la pagina completa siempre supera el umbral
    # gracias al texto del menu y del pie, y daria por buena una pagina vacia.
    $body = $response.Content
    $mainMatch = [regex]::Match($body, '<main[^>]*>(.*?)</main>', 'Singleline')

    if ($mainMatch.Success) {
        $body = $mainMatch.Groups[1].Value
    }

    $text = [regex]::Replace($body, '<[^>]+>', ' ')
    $text = [regex]::Replace($text, '\s+', ' ').Trim()

    if ($text.Length -lt 800) {
        Write-Result 'FALLO' $page.Name "Existe pero parece vacia o de relleno ($($text.Length) caracteres de texto)."
    } else {
        Write-Result 'OK' $page.Name "$($text.Length) caracteres de texto."
    }
}

# --- 5. Resumen ----------------------------------------------------------
Write-Host ''
Write-Host ('-' * 60)
Write-Host ("Correctas: {0}   Fallos: {1}   Avisos: {2}" -f $script:Pass, $script:Fail, $script:Warn)
Write-Host ''
Write-Host 'Esta comprobacion NO cubre: fuerza bruta contra wp-login.php,' -ForegroundColor DarkGray
Write-Host 'configuracion TLS, entrega real de correo ni revision juridica' -ForegroundColor DarkGray
Write-Host 'de los textos legales. Ver docs/auditoria-seguridad-codigo-2026-07-25.md' -ForegroundColor DarkGray
Write-Host ''

if ($script:Fail -gt 0) { exit 1 }
