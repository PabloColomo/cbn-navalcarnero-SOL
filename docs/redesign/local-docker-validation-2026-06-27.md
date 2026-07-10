# Validacion local Docker, WordPress y ACF

Fecha: 2026-06-27

## Objetivo

Confirmar que el entorno local Docker permite levantar WordPress, activar el
tema del proyecto, cargar el plugin MU `cbn-core`, instalar ACF y validar los
grupos JSON del modelo deportivo.

## Resultado

Estado: validado localmente.

## Entorno

- Sistema: Windows 10 Pro.
- Docker Desktop instalado en modo usuario.
- Docker: 29.5.3.
- Docker Compose: v5.1.4.
- Backend Docker: WSL 2.
- WordPress: imagen `wordpress:php8.3-apache`.
- Base de datos: imagen `mariadb:11`.
- WP-CLI: imagen `wordpress:cli-php8.3`.

## Pasos ejecutados

1. Instalacion de Docker Desktop desde el instalador oficial.
2. Arranque de Docker Desktop.
3. Validacion de `compose.yaml` con `docker compose config`.
4. Arranque de WordPress y MariaDB:

   ```bash
   docker compose up -d
   ```

5. Instalacion local de WordPress con WP-CLI.
6. Activacion del tema `cbn-theme`.
7. Verificacion del plugin MU `cbn-core`.
8. Instalacion y activacion local de ACF.
9. Validacion de grupos ACF JSON.
10. Creacion de contenido local de prueba:
    - Equipo.
    - Partido.
    - Sponsor.
11. Escritura y lectura de campos ACF con `update_field()` y `get_field()`.

## Validaciones realizadas

### Docker

Docker y Compose responden correctamente:

```text
Docker version 29.5.3
Docker Compose version v5.1.4
```

Contenedores levantados:

```text
cbn-database-1    mariadb:11                healthy
cbn-wordpress-1   wordpress:php8.3-apache   0.0.0.0:8080->80/tcp
```

### WordPress

WordPress responde en:

```text
http://localhost:8080
```

La home local responde con codigo `200` y titulo:

```text
Club Baloncesto Navalcarnero Local
```

El admin responde en:

```text
http://localhost:8080/wp-admin/
```

### Tema y MU plugin

Tema activo:

```text
cbn-theme
```

Plugin MU activo:

```text
cbn-core
```

CPTs detectados por WordPress:

```text
cbn_team       Equipos
cbn_player     Jugadores
cbn_match      Partidos
cbn_sponsor    Sponsors
cbn_document   Documentos
```

### ACF

Plugin instalado localmente:

```text
advanced-custom-fields  active  6.8.4
```

Grupos ACF JSON detectados por ACF:

```text
group_cbn_home_content      CBN Home Content
group_cbn_match_details     CBN Match Details
group_cbn_sponsor_details   CBN Sponsor Details
group_cbn_team_details      CBN Team Details
```

### Contenido de prueba

Se crearon registros locales:

```text
Equipo:  ID 4  Equipo Prueba Local
Sponsor: ID 5  Sponsor Prueba Local
Partido: ID 6  Partido Prueba Local
```

Campos ACF escritos y leidos correctamente:

```text
team_category=Senior Masculino
sponsor_active=true
match_status=scheduled
match_home_team=CB Navalcarnero
match_club_team=4
```

## Incidencias encontradas

### PATH de Docker tras instalar

Despues de instalar Docker Desktop, la terminal actual no tenia actualizado el
PATH y `docker-credential-desktop` no se encontraba.

Mitigacion usada:

```powershell
$dockerBin = Join-Path $env:LOCALAPPDATA 'Programs\DockerDesktop\resources\bin'
$env:Path = "$dockerBin;$env:Path"
```

Recomendacion:

- Reiniciar la terminal despues de instalar Docker Desktop.
- Si sigue fallando, usar temporalmente la ruta anterior.

### Permisos de WP-CLI al instalar plugins

WP-CLI no pudo crear el directorio del plugin ACF usando el usuario por defecto
del contenedor.

Mitigacion usada en local:

```bash
docker compose run --rm --user 0 wpcli wp plugin install advanced-custom-fields --activate --allow-root
```

Recomendacion:

- Mantener esta excepcion solo para entorno local Windows.
- No versionar plugins instalados.
- En staging/produccion instalar plugins desde el admin, Composer o el flujo de
  despliegue aprobado.

### Logs generados durante pruebas de quoting

Se produjeron errores PHP solo durante pruebas fallidas de `wp eval` por quoting
entre PowerShell, Docker Compose y WP-CLI.

Impacto:

- No indica fallo del tema ni de ACF.
- La validacion final con `wp eval-file` paso correctamente.

Recomendacion:

- Usar `wp eval-file` para scripts temporales de validacion en Windows.

## Archivos runtime generados

WordPress genero:

```text
wp-content/index.php
```

Contenido:

```php
<?php
// Silence is golden.
```

Se versiona porque es el placeholder estandar de WordPress para evitar listado
de directorios.

No se versionan:

- `wp-content/plugins/advanced-custom-fields/`
- `wp-content/upgrade/`
- `wp-content/uploads/`
- base de datos local.
- volumen de WordPress core.

## Criterio de aceptacion

Cumplido:

- Docker instalado y operativo.
- `docker compose config` valido.
- WordPress y MariaDB levantan.
- WordPress responde en `localhost:8080`.
- Tema `cbn-theme` activo.
- MU plugin `cbn-core` activo.
- CPTs del club registrados.
- ACF instalado localmente.
- ACF lee los grupos JSON del repo.
- Campos ACF se pueden escribir y leer.

## Siguiente paso recomendado

Con el entorno local validado, el siguiente paso tecnico es crear plantillas
WordPress basicas para contenido real:

1. Archivo de listado de equipos.
2. Archivo single de equipo.
3. Archivo de listado de partidos.
4. Archivo single de partido.
5. Archivo de sponsors.

Antes de construir esas plantillas conviene decidir si el MVP debe empezar por:

- equipos y partidos,
- home dinamica conectada a CPTs,
- o tienda/inscripciones con WooCommerce.
