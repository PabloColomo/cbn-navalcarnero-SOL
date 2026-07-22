# Entorno local Docker

Fecha: 2026-06-27

## Objetivo

Levantar un WordPress local reproducible para validar el tema `cbn-theme`, el
plugin MU `cbn-core`, ACF JSON, contenido deportivo, WooCommerce y futuras
integraciones sin depender de una instalacion manual en cada maquina.

## Requisito de sistema

Docker debe estar instalado en la maquina. En Windows, la opcion recomendada es
Docker Desktop con WSL 2 activo.

Comprobacion:

```bash
docker --version
docker compose version
```

Si esos comandos no existen, primero hay que instalar Docker Desktop y reiniciar
la terminal.

En Windows, el camino recomendado para una copia nueva es:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\bootstrap-local.ps1
```

Ese comando tambien compila el frontend, instala WordPress si hace falta,
activa el tema y crea/configura las paginas esenciales.

## Servicios

El archivo `compose.yaml` define:

- `wordpress`: WordPress con PHP 8.3 y Apache.
- `database`: MariaDB 11.
- `phpmyadmin`: herramienta opcional para revisar la base de datos.
- `wpcli`: herramienta opcional para administrar WordPress desde consola.

Los servicios opcionales viven bajo el perfil `tools`.

## Variables

Crear el archivo local:

```bash
cp .env.example .env
```

El archivo `.env` no se versiona. No debe contener secretos de produccion,
credenciales de federacion, tokens de Stripe ni accesos del dominio.

## Arranque basico

Levantar WordPress y base de datos:

```bash
docker compose up -d
```

Abrir:

```text
http://localhost:8080
```

Ver logs:

```bash
docker compose logs -f wordpress
```

Parar contenedores:

```bash
docker compose down
```

## Herramientas opcionales

Levantar tambien phpMyAdmin:

```bash
docker compose --profile tools up -d
```

Abrir:

```text
http://localhost:8081
```

Ejecutar WP-CLI:

```bash
docker compose run --rm wpcli wp --info
```

## Instalacion inicial de WordPress

`scripts/bootstrap-local.ps1` completa la instalacion local automaticamente y
genera una contrasena que solo se muestra en la terminal. Si se usa el flujo
manual, completar el instalador en el navegador.

Recomendaciones locales:

- Idioma: Espanol.
- Visibilidad en buscadores: desactivada.
- Usuario admin: solo para local, no reutilizar en produccion.
- Contrasena: local y no compartida.

## Validacion de ACF

ACF no se versiona como plugin. Se instala localmente para validar los grupos
JSON del tema.

El panel CBN incluye campos nativos de respaldo, por lo que ACF no es necesario
para ver la web ni para la gestion basica del contenido.

Instalar ACF desde WP-CLI:

```bash
docker compose run --rm wpcli wp plugin install advanced-custom-fields --activate
```

Validar despues:

1. Entrar en `http://localhost:8080/wp-admin`.
2. Activar el tema `CBN Theme` si no esta activo.
3. Crear un equipo de prueba.
4. Crear un partido de prueba.
5. Crear un sponsor de prueba.
6. Confirmar que aparecen los campos de ACF.
7. Confirmar que no hay errores en `wp-content/debug.log`.

## Validacion del tema

Compilar assets:

```bash
npm ci
npm run build
```

El directorio `assets/dist` no se versiona. Se genera en local y en CI cuando
corresponda. WordPress solo usa su manifest cuando es mas reciente que
`main.css` y `main.js`; si un `git pull` deja un build ignorado y antiguo, usa
el CSS fuente con version basada en `filemtime` hasta el siguiente build.

## Reset local

Para reiniciar contenedores sin borrar datos:

```bash
docker compose down
docker compose up -d
```

Para borrar la base de datos local y WordPress core descargado:

```bash
docker compose down -v
```

Este comando elimina datos locales. No usar si hay contenido de prueba que se
quiera conservar.

## Criterios de entorno listo

- `docker compose up -d` arranca sin errores.
- WordPress carga en `http://localhost:8080`.
- WordPress queda instalado mediante bootstrap o instalador manual.
- El tema `cbn-theme` se activa.
- El panel CBN funciona con campos nativos; ACF puede instalarse para validar sus JSON.
- Los CPTs de `cbn-core` aparecen en el admin.
- Se puede crear un equipo, partido y sponsor de prueba.
- `npm run verify` pasa.

## Limitaciones actuales

Este repositorio no instala Docker Desktop. La instalacion del motor Docker es
un requisito de la maquina.

Tampoco se versionan plugins instalados, uploads ni datos de base de datos. Esa
informacion pertenece al entorno local, staging o produccion.
