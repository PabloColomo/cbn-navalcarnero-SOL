# Arquitectura: federacion, dominio y continuidad del proyecto

Fecha: 2026-06-27

## Decision principal

Cuando lleguen el dominio y las credenciales de la federacion no se debe borrar ni rehacer el proyecto desde cero.

La base actual se mantiene:

- WordPress como CMS.
- Tema custom `cbn-theme`.
- MU plugin `cbn-core`.
- Home MVP.
- Sistema visual 60% blanco, 30% rojo y 10% negro.
- ACF JSON versionado.
- Flujo GitHub con rama, PR, CI y merge validado.
- Documentacion del proyecto.

Lo que se anadira son capas nuevas:

- Capa de dominio y despliegue.
- Capa de integracion con la federacion.
- Capa de importacion/sincronizacion de datos deportivos.
- Capa de administracion para revisar sincronizaciones y errores.

## Principio de arquitectura

La federacion debe ser la fuente oficial para datos deportivos.

```text
Federacion -> Importador -> WordPress -> Web publica
```

WordPress no debe inventar ni duplicar manualmente calendario, resultados o estadisticas si esos datos ya existen en la federacion.

WordPress debe actuar como:

- Cache publicable.
- Capa editorial.
- Capa de visibilidad.
- Capa para enriquecer datos con cronicas, fotos, destacados, sponsors o textos del club.

## Que no cambia cuando lleguen las credenciales

- No se borra la home.
- No se rehace el tema.
- No se elimina la documentacion.
- No se cambia el sistema visual por defecto.
- No se rompen los CPTs existentes.
- No se meten credenciales en Git.
- No se copia la web de la federacion.

## Que puede cambiar cuando se analice la federacion

- Campos exactos de `cbn_match`.
- Modelo de temporadas.
- Modelo de competiciones.
- Relacion entre equipos del club y equipos federativos.
- Formato de resultados.
- Disponibilidad de estadisticas individuales o por equipo.
- Frecuencia de sincronizacion.
- Necesidad de importacion manual, programada o mixta.

## Datos esperados de la federacion

Cuando lleguen las credenciales hay que identificar como se accede a:

- Calendario.
- Partidos.
- Resultados.
- Clasificaciones.
- Estadisticas.
- Equipos.
- Categorias.
- Competiciones.
- Temporadas.
- Instalaciones/pabellones.

Tambien hay que confirmar el formato:

- API oficial.
- Exportacion CSV/Excel.
- Panel privado con datos descargables.
- HTML autenticado.
- Endpoints internos.

No se debe hacer scraping autenticado sin confirmar que esta permitido por condiciones de uso o autorizacion del club/federacion.

## Modelo tecnico recomendado

### Identificadores externos

Cada dato importado debe guardar un identificador externo:

```text
external_source = federation
external_id = identificador del dato en federacion
external_updated_at = fecha/hora de ultima lectura
```

Esto permite:

- Actualizar sin duplicar.
- Detectar cambios.
- Saber que datos vienen de la federacion.
- Mantener trazabilidad.

### Estados de sincronizacion

Cada importacion deberia registrar:

- Fecha y hora.
- Fuente.
- Entidades leidas.
- Entidades creadas.
- Entidades actualizadas.
- Entidades omitidas.
- Errores.
- Duracion.

### Gestion editorial en WordPress

Datos que deberian venir de federacion:

- Fecha del partido.
- Hora.
- Competicion.
- Jornada.
- Equipo local.
- Equipo visitante.
- Resultado.
- Clasificacion.
- Estadisticas oficiales.

Datos que puede editar el club en WordPress:

- Cronica.
- Galeria.
- Imagen destacada.
- Partido destacado en home.
- Visibilidad publica.
- Texto editorial.
- CTA relacionado.
- Relacion con noticia.

## Seguridad de credenciales

Las credenciales de federacion no deben:

- Pegarse en el chat.
- Guardarse en Git.
- Guardarse en docs.
- Hardcodearse en PHP, JS, JSON o YAML.
- Imprimirse en logs.

Deben gestionarse mediante:

- Variables de entorno del hosting.
- Secretos del proveedor.
- Configuracion segura de WordPress.
- Usuario de solo lectura si la federacion lo permite.

Si se necesita configurar en WordPress, se debe estudiar cifrado o almacenamiento protegido antes de guardarlas.

## Dominio comprado

El dominio no obliga a rehacer nada. Se gestionara como parte de despliegue.

Antes de tocar DNS se necesita saber:

- Dominio exacto.
- Registrador.
- Si ya hay correo activo con ese dominio.
- Hosting previsto.
- Si se usara Cloudflare.
- Si se quiere `www` o dominio raiz como canonico.
- Si se necesita staging.

## Estrategia de entornos

Recomendacion:

```text
staging.dominio.com -> pruebas y validacion
dominio.com         -> produccion
www.dominio.com     -> redireccion canonica
```

No apuntar el dominio a produccion hasta validar:

- WordPress instalado.
- Tema activo.
- SSL activo.
- Backups configurados.
- Emails transaccionales.
- Formularios.
- WooCommerce/Stripe en modo test.
- Cache/CDN.
- Politicas legales.
- Contenido minimo real.

## DNS y correo

Antes de cambiar nameservers o registros hay que proteger correo:

- MX.
- SPF.
- DKIM.
- DMARC.
- Autodiscover si aplica.
- Webmail o proveedor de correo actual.

Riesgo principal:

- Romper el correo del club por apuntar DNS sin copiar registros existentes.

## Checklist cuando lleguen credenciales

No pedir ni guardar contrasenas en el repo. Se debe recopilar solo informacion operativa:

- URL de la plataforma federativa.
- Tipo de acceso.
- Usuario con permisos disponibles.
- Si existe API documentada.
- Si hay exportaciones.
- Si hay terminos de uso.
- Que competiciones/equipos del club deben importarse.
- Frecuencia de actualizacion deseada.
- Datos minimos para MVP.

Despues:

1. Auditar la plataforma en modo solo lectura.
2. Documentar endpoints/exportaciones disponibles.
3. Definir modelo definitivo de `cbn_match`.
4. Definir sincronizacion inicial manual.
5. Implementar importador en rama separada.
6. Probar con datos de staging.
7. Validar con el club.
8. Automatizar solo cuando la importacion manual sea fiable.

## Checklist cuando llegue el dominio

- Confirmar dominio exacto.
- Confirmar registrador.
- Exportar o capturar registros DNS actuales.
- Confirmar si hay correo activo.
- Confirmar hosting.
- Confirmar si se usara Cloudflare.
- Definir dominio canonico.
- Crear staging.
- Activar SSL.
- Validar web en staging.
- Planificar ventana de cambio DNS.
- Revisar produccion despues del cambio.

## Orden recomendado de siguientes PRs

1. Documentar dominio y federacion. Este documento.
2. Preparar modelo de campos deportivos flexible.
3. Crear campos ACF JSON para `cbn_match`, `cbn_team` y `cbn_sponsor`.
4. Crear pantalla o estructura de sincronizacion, todavia sin credenciales reales.
5. Auditar plataforma federativa cuando lleguen credenciales.
6. Implementar importador contra la fuente real.
7. Preparar staging y DNS.

## Politica de validacion visual y GitHub

Para cambios visuales:

1. Crear rama.
2. Aplicar cambios.
3. Build/preview.
4. Mostrar captura desktop y movil al responsable.
5. Esperar validacion.
6. Commit, push, PR, CI y merge.

Para cambios no visuales:

1. Crear rama.
2. Aplicar cambios.
3. Ejecutar checks disponibles.
4. Mostrar resumen/diff al responsable.
5. Esperar validacion.
6. Commit, push, PR, CI y merge.

## Veredicto

La base actual es valida y debe conservarse. El dominio y la federacion son integraciones externas que se suman al proyecto, no razones para reiniciarlo.
