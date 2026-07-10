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

Cuando WordPress arranque por primera vez, completar el instalador en el
navegador.

Recomendaciones locales:

- Idioma: Espanol.
- Visibilidad en buscadores: desactivada.
- Usuario admin: solo para local, no reutilizar en produccion.
- Contrasena: local y no compartida.

## Validacion de ACF

ACF no se versiona como plugin. Se instala localmente para validar los grupos
JSON del tema.

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
npm install
npm run build
```

El directorio `assets/dist` no se versiona. Se genera en local y en CI cuando
corresponda.

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
- El instalador de WordPress se completa.
- El tema `cbn-theme` se activa.
- ACF se instala y lee los JSON de `acf-json`.
- Los CPTs de `cbn-core` aparecen en el admin.
- Se puede crear un equipo, partido y sponsor de prueba.
- `npm run verify` pasa.

## Limitaciones actuales

Este repositorio no instala Docker Desktop. La instalacion del motor Docker es
un requisito de la maquina.

Tampoco se versionan plugins instalados, uploads ni datos de base de datos. Esa
informacion pertenece al entorno local, staging o produccion.
