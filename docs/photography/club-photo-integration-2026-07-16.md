# Integración fotográfica real del CBN

Fecha: 2026-07-16

## Inventario verificado

- 94 archivos JPEG originales, todos legibles, RGB y verticales.
- 92 archivos de 1059 × 1600 px y 2 archivos de 1059 × 1599 px.
- 66 hashes SHA-256 distintos.
- 28 copias exactas descartadas dentro de 28 pares binarios.
- 3 reexportaciones prácticamente idénticas descartadas:
  - `club-003` frente a `club-001`: mismo detalle con un recorte más cerrado.
  - `club-060` frente a `club-032`: misma toma con una exposición mucho más oscura.
  - `club-062` frente a `club-064`: mismo gesto de manos con menos luz.
- 63 escenas visualmente únicas, publicables y utilizadas.
- 0 archivos corruptos y 0 descartes por incompatibilidad técnica.

Los originales permanecen intactos en `images/`. El manifiesto de producción
`wp-content/themes/cbn-theme/assets/src/images/club/manifest.json` conserva los
identificadores, textos alternativos, focos, derivados y ubicaciones que
necesita el tema. Los nombres originales, hashes y detalle de descartes no se
publican en ese asset web; el generador fija internamente cada ID por SHA-256 y
el informe de auditoría conserva la trazabilidad completa.

## Dirección de arte

La fotografía se trata como relato de club, no como relleno: acción y emoción
en Inicio y Partidos; equipo, acompañamiento e identidad en El Club y Contacto;
concentración y variedad editorial en Equipos y Noticias; pertenencia en
Inscripción; detalles de equipación en Tienda sin presentarlos como productos;
y contexto de pista en Sponsors.

Los derivados mantienen la proporción vertical original. La presentación puede
usar recortes CSS no destructivos —conservando el foco curado— para construir
formatos editoriales cuadrados o panorámicos sin alterar los ficheros fuente.
Los heroes de Inicio y El Club usan tres paneles verticales en lugar de deformar
una toma para convertirla en panorámica.

Los nueve raíles inferiores conservan scroll nativo, foco visible y
`scroll-snap`, sin una dependencia JavaScript adicional, pero ya no repiten una
misma plantilla. Inicio usa secuencia cinematográfica; El Club, archivo impreso;
Equipos, alineación solapada; Partidos, ráfagas panorámicas; Noticias, mosaico a
dos alturas; Inscripción, arcos ascendentes; Tienda, pedestales cuadrados;
Contacto, tríptico conectado; y Sponsors, cartelería panorámica. Los índices y
categorías superpuestos en fotografía se sustituyen por el crédito discreto
`© CBN`, oculto a tecnologías de asistencia cuando sería repetitivo.

## Mapa de uso

| Ruta o superficie | Tratamiento                                                                                  | Fotografías visibles sin contenido CMS |
| ----------------- | -------------------------------------------------------------------------------------------- | -------------------------------------: |
| Inicio            | Hero de 3 paneles, tarjetas con crédito CBN y secuencia cinematográfica de formatos alternos |                                     23 |
| El Club           | Hero comunitario, 2 detalles neutros de pista y archivo de copias impresas                   |                                     18 |
| Equipos           | Alineación oscura y solapada; no se asigna una foto a una categoría concreta                 |                                     10 |
| Partidos          | Ráfagas panorámicas con cortes de retransmisión                                              |                                     13 |
| Noticias          | Mosaico editorial a dos alturas; las imágenes destacadas de WordPress mantienen prioridad    |                                      8 |
| Inscripción       | Galería ascendente con arcos de cantera                                                      |                                      8 |
| Tienda            | Hero identitario y 4 detalles en pedestales cuadrados, sin presentarlos como catálogo        |                                      5 |
| Contacto          | Tríptico conectado de equipo y acompañamiento                                                |                                      3 |
| Sponsors          | Cartelería panorámica de competición y contexto de pista; logos oficiales intactos           |                                      4 |

Las rutas legales, documentación, búsqueda y 404 no incorporan fotografía
adicional porque no existe una relación semántica natural. El conjunto de las
nueve superficies anteriores renderiza las 63 escenas únicas al menos una vez.
Además, las 63 forman parte de algún rail editorial incondicional: seguirán
apareciendo aunque el club añada heroes o imágenes destacadas propias al CMS.

## Cursor y acceso

El cursor circular animado con el escudo se ha retirado por completo del DOM,
del JavaScript y de la configuración del tema. En dispositivos con puntero fino
se usa una flecha convencional de 24 px en granate CBN (`#930012`), con contorno
blanco y fallback al cursor nativo. Campos editables, controles deshabilitados,
dispositivos táctiles y el modo de colores forzados conservan su cursor nativo
adecuado.

## Derivados

Por cada escena publicable se generan dos anchos y tres formatos:

- 480 y 960 px de ancho, sin ampliación ni recorte.
- JPEG progresivo como fallback.
- WebP y AVIF para navegadores compatibles.
- Orientación física normalizada con `exif_transpose`.
- Metadatos EXIF eliminados al recodificar.
- Ajuste limitado: sombras oscuras hasta un máximo de +10 %, contraste +3,5 %,
  color +2,5 % y enfoque +8 %.

El lote contiene 378 derivados. El helper `inc/photo-library.php` genera
`<picture>`, `srcset`, `sizes`, dimensiones explícitas, carga diferida y foco
curado. Solo la primera imagen del hero de cada página recibe
`fetchpriority="high"`; el resto de imágenes del primer hero se carga sin esa
prioridad y el contenido fuera del primer viewport usa `loading="lazy"`.

## Privacidad y precedencia editorial

- No se publican nombres, edades, equipos, temporadas ni identidades inferidas.
- Los textos alternativos describen únicamente la escena visible.
- La autorización de uso indicada por el responsable del proyecto sigue siendo
  el requisito de publicación; el manifiesto no sustituye el registro legal de
  consentimientos del club.
- Las imágenes reales gestionadas por WordPress tienen prioridad sobre los
  fallbacks curados del tema.
- Las imágenes destacadas de sponsors continúan tratándose como logos.

## Regeneración

Ejecutar desde la raíz del repositorio con una instalación de Pillow que tenga
soporte WebP y AVIF:

```powershell
python scripts/prepare-club-photos.py
```

El script valida primero los 66 hashes aprobados, no solo la cifra de archivos,
y liga cada hash a un ID estable. Genera todo el lote en un directorio de
staging y solo sustituye el directorio público cuando derivados y manifiesto
están completos. Si el inventario cambia o la generación falla, conserva la
biblioteca publicada anterior.
