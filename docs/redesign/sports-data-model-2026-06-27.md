# Modelo flexible de datos deportivos

Fecha: 2026-06-27

## Objetivo

Preparar WordPress para gestionar equipos, partidos y sponsors sin depender
todavia del formato exacto de la federacion.

Este paso no implementa importadores, automatizaciones ni consultas externas. La
meta es que el modelo interno ya pueda recibir datos oficiales cuando lleguen
credenciales, sin rehacer CPTs, taxonomias o campos principales.

## Alcance incluido

- ACF JSON para `cbn_match`.
- ACF JSON para `cbn_team`.
- ACF JSON para `cbn_sponsor`.
- Separacion entre datos oficiales, datos editoriales y trazabilidad externa.
- Campos de respaldo de texto para temporada, categoria, competicion e
  instalacion aunque tambien existan taxonomias.

## Alcance excluido

- No se conecta con la federacion.
- No se guardan credenciales.
- No se hace scraping.
- No se crean plantillas nuevas.
- No se modifica la home.
- No se instala ACF ni ningun plugin adicional.

## Estructura actual

El plugin MU `cbn-core` ya registra:

- `cbn_team`.
- `cbn_player`.
- `cbn_match`.
- `cbn_sponsor`.
- `cbn_document`.

Tambien registra taxonomias:

- `cbn_season`.
- `cbn_sport_category`.
- `cbn_competition`.
- `cbn_venue`.
- `cbn_sponsor_tier`.

Los grupos ACF viven en:

```text
wp-content/themes/cbn-theme/acf-json/
```

## Grupos ACF anadidos

### CBN Match Details

Archivo:

```text
wp-content/themes/cbn-theme/acf-json/group_cbn_match_details.json
```

Ubicacion:

- `post_type == cbn_match`.

Campos principales:

- Estado del partido.
- Fecha.
- Hora.
- Jornada.
- Equipo del club relacionado.
- Equipo local.
- Equipo visitante.
- Si Navalcarnero juega como local.
- Competicion visible.
- Temporada visible.
- Instalacion y direccion.
- Marcador local y visitante.
- Resultado validado.
- Mostrar en portada.
- Etiqueta destacada.
- Titulo publico alternativo.
- Cronica relacionada.
- URL de cronica externa.
- Notas publicas.
- Fuente externa.
- ID externo.
- URL externa.
- Ultima actualizacion externa.
- Huella externa.
- Estado de sincronizacion.

### CBN Team Details

Archivo:

```text
wp-content/themes/cbn-theme/acf-json/group_cbn_team_details.json
```

Ubicacion:

- `post_type == cbn_team`.

Campos principales:

- Nombre en federacion.
- Categoria visible.
- Temporada visible.
- Competicion visible.
- Instalacion habitual.
- Orden editorial.
- Entrenador principal.
- Cuerpo tecnico.
- Horario de entrenamiento.
- Mostrar plantilla publica.
- Mostrar en portada.
- Etiqueta destacada.
- Jugadores relacionados.
- Notas de plantilla.
- Fuente externa.
- ID externo.
- URL externa.
- Ultima actualizacion externa.
- Huella externa.
- Estado de sincronizacion.

### CBN Sponsor Details

Archivo:

```text
wp-content/themes/cbn-theme/acf-json/group_cbn_sponsor_details.json
```

Ubicacion:

- `post_type == cbn_sponsor`.

Campos principales:

- Nombre visible.
- Enlace.
- Texto alternativo del logo.
- Nivel visible.
- Descripcion publica.
- Sponsor activo.
- Mostrar en portada.
- Ubicacion preferente.
- Orden editorial.
- Fecha de inicio.
- Fecha de fin.
- Fuente externa.
- ID externo.
- URL externa.
- Ultima actualizacion externa.
- Estado de sincronizacion.

## Separacion de responsabilidades

### Datos oficiales

Datos que deberian venir de federacion si la fuente lo permite:

- Fechas y horas de partidos.
- Equipos local y visitante.
- Competicion.
- Jornada.
- Instalacion.
- Resultados.
- Estados del partido.
- Identificadores oficiales.

Estos campos deben poder actualizarse por importador cuando exista una fuente
validada.

### Datos editoriales

Datos que puede controlar el club desde WordPress:

- Mostrar en portada.
- Etiquetas destacadas.
- Notas publicas.
- Cronicas relacionadas.
- Titulos publicos alternativos.
- Orden editorial.
- Descripciones de sponsors.
- Visibilidad de plantilla.

Estos campos no deben ser pisados automaticamente por la federacion salvo que
se defina una regla explicita.

### Trazabilidad externa

Cada entidad preparada para sincronizacion incluye:

- `external_source`.
- `external_id`.
- `external_url`.
- `external_updated_at`.
- `external_hash`.
- `external_sync_status`.

La regla principal es que `external_id` debe ser estable y suficiente para hacer
upsert sin duplicar contenido.

## Campos de respaldo vs taxonomias

El modelo mantiene taxonomias para estructura editorial y URLs:

- Temporadas.
- Categorias.
- Competiciones.
- Instalaciones.
- Niveles de sponsor.

Tambien incluye campos de texto de respaldo porque todavia no conocemos el
formato final de federacion. Esto permite importar una primera version aunque
las taxonomias no esten normalizadas.

Cuando se conozca la fuente oficial, el siguiente paso sera decidir que valores
se convierten en terminos y cuales quedan como texto literal.

## Compatibilidad con ACF Free

Los grupos evitan campos de ACF Pro como repeater, flexible content, gallery o
clone.

Se usan campos normales:

- Text.
- Textarea.
- Select.
- True/false.
- Number.
- Date picker.
- Time picker.
- Post object.
- Relationship.

Si el entorno final no tiene algun campo disponible, se debera validar en
staging antes de cargar contenido real.

## Estrategia para la federacion

Cuando lleguen las credenciales:

1. Acceder en modo solo lectura.
2. Confirmar si existe API, export CSV/Excel o panel descargable.
3. Documentar campos disponibles y permisos.
4. Mapear IDs oficiales con `external_id`.
5. Probar importacion en dry-run.
6. Activar upsert solo cuando no duplique equipos o partidos.

No se debe guardar ninguna credencial en Git, ACF, capturas publicas ni logs.

## Criterio de aceptacion de este paso

- Los grupos ACF JSON existen y son validos como JSON.
- `npm run format` deja los archivos normalizados.
- `npm run verify` pasa.
- No se introduce dependencia de ACF Pro.
- No se cambia el frontend.
- El modelo permite trabajo manual ahora e importacion oficial despues.

## Siguiente paso recomendado

Validar estos grupos en un WordPress local o staging con ACF activo:

1. Crear un equipo de prueba.
2. Crear un partido de prueba.
3. Crear un sponsor de prueba.
4. Confirmar que los campos aparecen en admin.
5. Confirmar que no hay errores PHP.
6. Documentar cualquier ajuste necesario antes de construir plantillas.
