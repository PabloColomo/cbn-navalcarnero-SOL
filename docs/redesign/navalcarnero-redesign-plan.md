# Plan profesional de rediseno web para Club Baloncesto Navalcarnero

Fecha de investigacion: 2026-06-27

## 1. Resumen ejecutivo

Este documento propone un rediseno original para la web del Club Baloncesto Navalcarnero usando Manchester Basketball Club como referencia conceptual, no como fuente de copia. La referencia aporta energia deportiva, jerarquia visual, estructura modular, presencia de resultados/calendario, narrativa de club, sponsors y llamadas a la accion. Navalcarnero necesita una version mas realista, mantenible y adaptada a un club local con cantera, multiples equipos, inscripciones, noticias y gestion sencilla por personal no tecnico.

La recomendacion principal es construir una web WordPress con tema ligero o custom controlado, ACF para contenido estructurado, formularios gestionables, un sistema sencillo de partidos/resultados/equipos y animaciones selectivas con GSAP + ScrollTrigger y Lenis. SportsPress puede ser util, pero debe evaluarse con cuidado para evitar complejidad si los datos deportivos se gestionan manualmente.

## 2. Objetivo del rediseno

Objetivo de producto: convertir la web en el centro digital del club, no solo en un tablon de noticias. La web debe ayudar a familias, jugadores, entrenadores, sponsors y nuevos interesados a encontrar rapidamente informacion actualizada, entender la identidad del CBN y completar acciones clave.

Objetivos concretos:

- Modernizar la percepcion de marca del Club Baloncesto Navalcarnero.
- Dar protagonismo a equipos, cantera, resultados, calendario, inscripciones y sponsors.
- Mejorar la navegacion movil y la claridad de contenidos.
- Facilitar la actualizacion por parte del club sin depender de desarrollo para cada noticia o partido.
- Mantener buen rendimiento, accesibilidad y SEO.
- Evitar una web cara o sobreanimada que el club no pueda mantener.

## 3. Investigacion de la referencia Manchester Basketball Club

### Hechos observados

- La home de Manchester presenta una navegacion editorial y deportiva: Home, Roster, Scores & Schedule, About, Partners & Sponsors, Newsroom y Shop.
- Usa una CTA persistente de captacion, "Join the Team", muy visible en cabecera y menu.
- La primera pantalla combina lema de marca, anuncio del siguiente partido, imagenes/video y una sensacion de campana deportiva.
- La home integra narrativa institucional, CTA de captacion, match reports, league table, tienda, noticias, redes sociales, partners y newsletter.
- La seccion Roster permite filtrar por equipos y roles.
- Results y Fixtures tienen filtros por equipos y ligas.
- Fixtures muestra fecha, hora, venue y enfrentamiento.
- League Tables usa columnas deportivas como PL, WIN, LOSSES, WIN%, FOR, AGAINST, DIFF y PTS.
- Partners & Sponsors diferencia partners principales, sponsors de equipos y oportunidades comerciales.
- El footer refuerza partners, newsletter y enlaces clave.

### Elementos relevantes como inspiracion

Estructura visual: bloques grandes, ritmo de secciones, titulares fuertes, combinacion de deporte, comunidad y datos competitivos.

Navegacion: menu jerarquico con secciones de alto valor deportivo y CTA principal clara.

Hero: primera pantalla con identidad fuerte, siguiente partido y recursos visuales de equipo.

Animaciones: transiciones, movimiento al hacer scroll, textos destacados y carruseles para dar energia sin depender de mucho texto.

Tipografia: uso de titulares expresivos y mayusculas para generar caracter competitivo.

Color: paleta oscura/calida en la referencia; para Navalcarnero debe reinterpretarse con los colores propios del escudo CBN: negro, blanco, rojo, amarillo/oro y acentos naranja.

Noticias: tarjetas editoriales con categoria, fecha y titular; mejor que una lista basica.

Resultados, fixtures y tablas: contenido deportivo presentado como producto central, no como pagina secundaria.

Sponsors: partners visibles, con jerarquia y posibilidad de convertir la pagina en herramienta comercial.

CTA: captacion de jugadores, pruebas, campus, inscripciones, contacto y patrocinio.

Experiencia movil: la referencia sugiere una experiencia muy visual y de menu completo; para Navalcarnero debe ser mas ligera y directa.

## 4. Necesidades funcionales del Navalcarnero

### Hechos confirmados en la web actual

- La web actual tiene navegacion principal: Inicio, El Club, Noticias, Resultados, Equipos, Documentacion y Galerias.
- La pagina de noticias permite filtrar por categoria y contiene noticias recientes como horarios, matricula, calendario de partidos, busqueda de talento y entrenadores.
- La pagina de equipos lista temporadas y categorias para 2025/2026 y 2024/2025.
- La pagina El Club describe el foco en promover y divulgar el baloncesto en Navalcarnero y potenciar el deporte de base.
- Las instalaciones principales publicadas son el Pabellon Municipal La Estacion y pabellones del Colegio Maria Martin.
- Aparecen colaboradores/patrocinadores como Ayuntamiento de Navalcarnero, ELEVA Planificacion y Entrenamiento, Domino's Pizza Navalcarnero y Multiopticas Navalcarnero.

### Necesidades probables

- Equipos por temporada, categoria, genero y competicion.
- Jugadores y cuerpo tecnico por equipo, con ficha publica opcional.
- Calendario de partidos por equipo y temporada.
- Resultados y cronicas.
- Clasificaciones o enlace/importacion desde competiciones oficiales.
- Noticias, eventos y comunicados.
- Cantera, escuela y babybasket.
- Inscripciones para temporada, pruebas, campus y tecnificacion.
- Documentacion descargable.
- Patrocinadores y propuesta comercial.
- Contacto, localizacion, formularios y redes sociales.
- Galerias y contenido social, si el club puede mantenerlo.
- Area de avisos urgentes: horarios, cambios de entreno, convocatoria, pagos o plazos.

## 5. Propuesta de direccion visual

La direccion visual debe ser deportiva, local y familiar. No debe parecer una copia de Manchester ni una web premium inalcanzable para el club.

Principios:

- Identidad CBN en primer plano: escudo, colores rojo/negro/amarillo, referencias a Navalcarnero y cantera.
- Energia visual con titulares potentes, fondos contrastados y fotografia real del club.
- Modularidad: cada bloque debe funcionar con contenido real aunque falten fotos profesionales.
- Menos efectos, mas claridad: animacion como refuerzo, no como obstaculo.
- Sensacion de club vivo: proximos partidos, ultimas noticias, equipos, inscripciones y sponsors visibles desde home.

Paleta recomendada:

- Negro profundo: fondos premium y secciones de impacto.
- Blanco: legibilidad y secciones informativas.
- Rojo CBN: acento competitivo y CTAs.
- Amarillo/oro: energia, marcadores, estados destacados.
- Gris claro: tablas, fondos secundarios y contenido administrativo.

Tipografia recomendada:

- Titulares: sans condensada o display deportiva con licencia adecuada.
- Texto y UI: sans legible tipo Inter, Manrope, General Sans o similar.
- Evitar fuentes demasiado decorativas para contenidos largos o datos deportivos.

## 6. Arquitectura de informacion sugerida

Navegacion principal:

- Inicio
- Club
- Equipos
- Partidos
- Noticias
- Escuela y cantera
- Sponsors
- Contacto

Subnavegacion recomendada:

- Club: Historia, Instalaciones, Directiva/cuerpo tecnico si se publica, Documentacion.
- Equipos: Temporada actual, categorias, cuerpo tecnico, plantillas.
- Partidos: Calendario, Resultados, Clasificaciones.
- Escuela y cantera: Inscripciones, Babybasket, campus, tecnificacion.
- Sponsors: Partners, patrocinio, ventajas, contacto comercial.

CTAs persistentes:

- Inscribete / Prueba con el CBN.
- Ver partidos.
- Contactar.
- Patrocina al club.

## 7. Stack tecnico recomendado

Base:

- WordPress como CMS.
- Tema custom ligero o child theme sobre GeneratePress. Bricks puede ser opcion si el club necesita edicion visual avanzada, pero debe limitarse con plantillas y roles.
- ACF Pro para modelos de contenido, relaciones y bloques.
- Custom Post Types propios para equipos, jugadores, partidos, sponsors y documentos.
- SportsPress solo si el club necesita gestion deportiva completa dentro de WordPress y acepta su curva de mantenimiento.

Frontend:

- SCSS/CSS modular compilado por Vite o pipeline del tema.
- JavaScript modular sin jQuery obligatorio salvo dependencias existentes.
- GSAP + ScrollTrigger para animaciones controladas.
- Lenis para scroll suave, con desactivacion en reduced motion y moviles si afecta rendimiento.
- Swiper para carruseles de noticias, sponsors, equipos y galeria.

Formularios:

- Fluent Forms como opcion ligera o Gravity Forms si se prioriza ecosistema avanzado.
- Formularios separados para contacto, pruebas, inscripciones, campus y patrocinio.

Infraestructura:

- Cloudflare para DNS, WAF, cache y proteccion basica.
- Plugin de cache segun hosting: WP Rocket, LiteSpeed Cache o FlyingPress.
- Backups automaticos externos.
- Staging antes de cambios mayores.

SEO y analitica:

- Rank Math o Yoast SEO.
- Google Search Console.
- GA4 o alternativa mas simple si el club quiere minimizar tracking.
- Schema para Organization, SportsTeam, SportsEvent, NewsArticle y FAQ cuando aplique.

## 8. Modelo de contenidos en WordPress

### Tipos de contenido

Equipo:

- Nombre.
- Temporada.
- Categoria.
- Genero.
- Competicion.
- Division.
- Foto de equipo.
- Entrenadores relacionados.
- Jugadores relacionados.
- Horarios de entrenamiento.
- Instalacion.
- Orden en listados.

Jugador:

- Nombre publico.
- Foto.
- Equipo(s).
- Dorsal.
- Posicion.
- Ano de nacimiento opcional.
- Altura opcional.
- Bio breve opcional.
- Estado: publicado, oculto, pendiente.

Staff:

- Nombre.
- Cargo.
- Equipo(s).
- Foto opcional.
- Bio breve.
- Email publico solo si el club lo aprueba.

Partido:

- Equipo CBN.
- Rival.
- Fecha y hora.
- Local/visitante.
- Instalacion.
- Competicion.
- Jornada/fase.
- Estado: programado, aplazado, jugado, cancelado.
- Resultado CBN.
- Resultado rival.
- Cronica relacionada.
- Enlace federativo.

Clasificacion:

- Temporada.
- Competicion.
- Categoria/equipo.
- Filas de tabla o fuente externa.
- Fecha de actualizacion.

Noticia:

- Titulo.
- Categoria.
- Equipo relacionado.
- Temporada.
- Imagen destacada.
- Extracto.
- CTA opcional.

Sponsor:

- Nombre.
- Logo.
- Nivel: institucional, principal, equipo, colaborador.
- URL.
- Descripcion.
- Equipo patrocinado opcional.
- Orden.

Documento:

- Titulo.
- Tipo: inscripcion, normativa, autorizacion, calendario, imagen.
- Archivo.
- Temporada.
- Categoria.

Formulario/landing:

- Tipo de formulario.
- Temporada.
- Fecha limite.
- Responsable interno.
- Mensaje de confirmacion.

### Taxonomias

- Temporada.
- Categoria deportiva.
- Genero.
- Competicion.
- Instalacion.
- Tipo de noticia.
- Nivel de sponsor.
- Tipo de documento.

### Relaciones

- Equipo tiene jugadores, staff, partidos, noticias y sponsors.
- Partido pertenece a equipo, competicion, temporada e instalacion.
- Noticia puede relacionarse con equipo, partido, campus o formulario.
- Sponsor puede relacionarse con equipo, evento o pagina comercial.
- Documento puede relacionarse con temporada, equipo o inscripcion.

## 9. Componentes principales de la web

Home:

- Hero con escudo CBN, lema propio, fotografia/video del club y CTA de inscripcion.
- Banda de proximo partido o aviso importante.
- Accesos rapidos: equipos, calendario, inscripciones, contacto.
- Ultimas noticias con categoria y fecha.
- Bloque de resultados/proximos partidos.
- Bloque de cantera/escuela.
- Sponsors destacados.
- CTA final de contacto o prueba.

Equipos:

- Selector por temporada.
- Filtros por categoria y genero.
- Tarjetas de equipo con foto, competicion, entrenador y CTA.
- Pagina detalle de equipo con plantilla, staff, partidos y noticias relacionadas.

Jugadores:

- Listado filtrable por equipo.
- Ficha publica controlada.
- Politica de privacidad clara para menores: publicar solo lo aprobado por el club/familias.

Partidos:

- Calendario por equipo.
- Vista lista y vista mensual opcional.
- Datos esenciales: fecha, hora, rival, instalacion, competicion.

Resultados:

- Listado por equipo y temporada.
- Marcadores claros.
- Enlace a cronica o acta federativa si existe.

Clasificacion:

- Tablas por competicion.
- Fecha de actualizacion visible.
- Si el dato es externo, mostrar fuente/enlace.

Noticias:

- Listado editorial con filtros por categoria.
- Plantillas para comunicados, partidos, eventos, campus, inscripciones y sponsors.
- Bloques relacionados con equipos y formularios.

Sponsors:

- Jerarquia de logos.
- Pagina comercial con beneficios de patrocinio.
- CTA "Patrocina al CBN".

Formularios:

- Contacto general.
- Prueba con el club.
- Inscripcion temporada.
- Campus/tecnificacion.
- Patrocinio.

Footer:

- Escudo, contacto, direccion, redes, enlaces legales, sponsors principales y newsletter opcional.

## 10. Plan de diseno UX/UI

Fase de UX:

- Auditar contenidos actuales.
- Definir usuarios principales: familias, jugadores, entrenadores, sponsors, visitantes y administradores del club.
- Mapear tareas prioritarias por usuario.
- Crear sitemap y flujos para inscripcion, busqueda de equipo, consulta de partido y patrocinio.

Fase UI:

- Crear sistema visual propio CBN.
- Disenar home, equipo detalle, calendario, noticia, sponsor y formulario.
- Preparar variantes movil primero para navegacion, partidos y formularios.
- Definir estados: vacio, cargando, aplazado, resultado pendiente, error de formulario.

Reglas de diseno:

- No copiar layouts exactos de Manchester.
- Mantener el escudo y colores del CBN como base.
- Usar fotografia real del club como activo principal.
- Evitar carruseles criticos en movil.
- Priorizar CTA simple y texto claro.

## 11. Plan de desarrollo frontend

- Crear theme o child theme con estructura modular.
- Definir tokens CSS: colores, tipografia, espaciado, botones, tablas, tarjetas y formularios.
- Crear componentes reutilizables:
  - Header.
  - Menu movil.
  - Hero.
  - Ticker de proximo partido.
  - Card de noticia.
  - Card de equipo.
  - Card de partido.
  - Tabla de clasificacion.
  - Sponsor strip.
  - Form block.
  - Footer.
- Usar Swiper solo donde mejore el contenido.
- Cargar JS por pagina/componente.
- Implementar `prefers-reduced-motion`.
- Evitar dependencias globales innecesarias.

## 12. Plan de desarrollo WordPress/backend

- Registrar CPTs y taxonomias en plugin propio del club, no dentro del tema, para no perder datos al cambiar diseno.
- Configurar ACF Field Groups versionados en JSON.
- Crear plantillas de archivo y detalle para cada CPT.
- Definir roles:
  - Administrador tecnico.
  - Gestor de contenido.
  - Editor de noticias.
  - Responsable deportivo.
- Configurar formularios con notificaciones y almacenamiento seguro.
- Preparar staging y migracion antes de produccion.
- Documentar como crear equipo, jugador, partido, noticia y sponsor.

## 13. Plan de animaciones e interaccion

Animaciones recomendadas:

- Entrada suave de titulares y bloques principales.
- Ticker horizontal de proximo partido o avisos.
- Hover deportivo en tarjetas de equipo/noticia.
- Transiciones de filtros sin recargar pagina cuando sea razonable.
- Microinteracciones en botones y menus.

Animaciones a evitar o limitar:

- Cursor personalizado permanente.
- Image trails pesados.
- Scroll hijacking.
- Parallax intenso en movil.
- Animaciones que oculten contenido hasta que JS cargue.

Reglas:

- Todo contenido debe ser usable sin animacion.
- Desactivar o simplificar con `prefers-reduced-motion`.
- Medir impacto en Core Web Vitals.

## 14. Plan de SEO, rendimiento y accesibilidad

SEO:

- Arquitectura limpia de URLs.
- Metadatos por equipo, partido, noticia y sponsor.
- Schema Organization, SportsTeam, SportsEvent y NewsArticle.
- Sitemap XML.
- Redirecciones desde URLs antiguas.
- Open Graph con imagenes propias del club.

Rendimiento:

- Imagenes WebP/AVIF con fallback.
- Lazy loading para galerias y listados.
- CSS critico para home.
- JS dividido por componente.
- Cache de pagina y CDN Cloudflare.
- Limitar plugins y peticiones externas.

Accesibilidad:

- Contraste AA.
- Navegacion por teclado.
- Foco visible.
- Formularios con labels reales y mensajes claros.
- Tablas deportivas accesibles con encabezados.
- Alternativas textuales para imagenes.
- No depender solo de color para estados de partido.

Seguridad:

- Backups automaticos.
- 2FA para administradores.
- WAF Cloudflare.
- Plugins actualizados.
- Principio de minimo privilegio para usuarios.

## 15. Plan de migracion de contenido

Inventario:

- Noticias actuales.
- Equipos y categorias.
- Documentos.
- Galerias.
- Resultados y calendarios.
- Sponsors.
- Datos legales/contacto.

Proceso:

1. Exportar contenido actual o extraerlo de forma estructurada.
2. Limpiar duplicados y contenidos obsoletos.
3. Mapear cada tipo de contenido al nuevo modelo.
4. Migrar primero contenido institucional y equipos.
5. Migrar noticias recientes y dejar archivo historico si es util.
6. Subir assets optimizados.
7. Revisar enlaces y redirecciones.
8. Validar con responsables del club antes de publicar.

## 16. Fases del proyecto

Fase 0: Descubrimiento y alcance

- Validar objetivos.
- Confirmar roles del club.
- Recopilar fotos, escudo, colores, contenido y necesidades de formularios.

Fase 1: UX y contenido

- Sitemap.
- Wireframes.
- Modelo de contenidos.
- Inventario de migracion.

Fase 2: Direccion visual

- Moodboard original.
- Home y paginas clave.
- Sistema de componentes.

Fase 3: Desarrollo base

- WordPress, tema, CPTs, ACF, estilos base.
- Header, footer y plantillas principales.

Fase 4: Modulos deportivos

- Equipos, jugadores, partidos, resultados y clasificaciones.

Fase 5: Formularios y contenido comercial

- Inscripciones, contacto, patrocinio, sponsors y documentacion.

Fase 6: Migracion y QA

- Contenido real.
- Pruebas responsive, rendimiento, accesibilidad y SEO.

Fase 7: Lanzamiento y formacion

- Deploy.
- Monitorizacion.
- Manual de uso.
- Sesion de formacion para el club.

## 17. Backlog priorizado

### Imprescindible

- Home moderna con CTA principal.
- Header y menu movil.
- Paginas Club, Equipos, Noticias, Contacto y Sponsors.
- CPT Equipos.
- CPT Partidos basico.
- Noticias con categorias.
- Formularios de contacto e inscripcion/prueba.
- Footer legal y contacto.
- SEO basico y redirecciones.
- Optimizacion movil.

### Recomendable

- Jugadores y staff por equipo.
- Resultados y calendario filtrable.
- Clasificaciones.
- Bloques ACF reutilizables.
- Newsletter.
- Galeria editorial.
- Sponsors por nivel.
- Manual de edicion.
- Integracion con redes o embeds seleccionados.

### Futuro

- Tienda o merchandising.
- Area privada para familias.
- Pagos online.
- Automatizacion con datos federativos si hay fuente viable.
- App/PWA ligera.
- Estadisticas avanzadas por jugador/equipo.
- Multidioma.

## 18. Riesgos y mitigaciones

Copiar demasiado la referencia:

- Mitigacion: usar solo patrones generales; crear identidad visual CBN desde cero.

Sobrecargar la web con animaciones:

- Mitigacion: animaciones por componente, reduced motion y presupuesto de rendimiento.

Rendimiento pobre:

- Mitigacion: imagenes optimizadas, JS modular, cache, CDN y auditorias Lighthouse.

Mantenimiento dificil en WordPress:

- Mitigacion: CPTs claros, ACF JSON versionado, roles limitados y manual de edicion.

Dependencia excesiva de plugins:

- Mitigacion: plugin propio para datos criticos; usar plugins solo para formularios, SEO/cache y funciones deportivas si aportan valor real.

Falta de contenido real:

- Mitigacion: disenar componentes que funcionen con poco contenido y definir checklist de fotos/datos antes de desarrollo final.

Complejidad para usuarios no tecnicos:

- Mitigacion: pantallas de administracion simples, campos guiados y plantillas cerradas.

Privacidad de menores:

- Mitigacion: aprobacion explicita del club/familias, perfiles publicos limitados y opcion de ocultar datos/fotos.

Datos deportivos desactualizados:

- Mitigacion: responsable interno por temporada/equipo y fecha de ultima actualizacion visible.

## 19. Preguntas pendientes para cerrar alcance

- El club quiere publicar fichas individuales de jugadores, o solo plantillas por equipo?
- Hay autorizacion para usar fotos de menores en la nueva web?
- Quien actualizara partidos, resultados y noticias?
- Se necesita tienda online real o solo enlace externo?
- Las inscripciones requieren pago online o solo formulario?
- Se usara SportsPress o se prefiere estructura propia simplificada?
- Hay acceso a fuente oficial de datos de la Federacion de Baloncesto de Madrid?
- Que hosting WordPress se usara?
- Que plugins actuales existen y cuales se quieren conservar?
- Hay manual de marca, colores exactos o archivos vectoriales del escudo?
- Que sponsors deben aparecer en home y con que jerarquia contractual?
- La web debe estar lista para una fecha concreta de temporada?

## 20. Proximos pasos recomendados

1. Aprobar este alcance base y decidir si el MVP sera "web institucional + equipos + noticias + formularios" o si incluira resultados/clasificaciones completas.
2. Reunir contenido real: escudo vectorial, fotos, sponsors, equipos 2025/2026, instalaciones, textos legales y formularios necesarios.
3. Elegir enfoque tecnico: GeneratePress child/custom theme con ACF, o Bricks con plantillas bloqueadas.
4. Definir modelo de datos deportivo final: CPT propio o SportsPress.
5. Crear wireframes de home, equipos, partido/calendario, noticia y formulario.
6. Disenar una direccion visual original para CBN.
7. Estimar desarrollo por fases y publicar primero un MVP mantenible.

## Partes a adaptar, simplificar o mejorar respecto a Manchester

Adaptar:

- CTA principal de captacion.
- Hero deportivo con proximo partido/aviso.
- Secciones de equipos, noticias, resultados y sponsors.
- Energia tipografica y ritmo de scroll.

Simplificar:

- Menus y animaciones complejas.
- Cursor personalizado.
- Trail de imagenes.
- Dependencia de multiples librerias de animacion.
- Shop si no existe una tienda real.

Mejorar para Navalcarnero:

- Gestion editorial mas simple.
- Formularios de inscripcion y prueba mas visibles.
- Cantera y escuela como contenido central.
- Contenido legal, privacidad y permisos de menores.
- Datos deportivos con fecha de actualizacion.

## Fuentes consultadas

- Manchester Basketball Club home: https://manchesterbasketballclub.co.uk/
- Manchester Basketball Club roster: https://manchesterbasketballclub.co.uk/roster/
- Manchester Basketball Club results: https://manchesterbasketballclub.co.uk/results/
- Manchester Basketball Club fixtures: https://manchesterbasketballclub.co.uk/fixtures/
- Manchester Basketball Club league tables: https://manchesterbasketballclub.co.uk/league-tables/
- Manchester Basketball Club partners: https://manchesterbasketballclub.co.uk/partners-sponsors/
- Club Baloncesto Navalcarnero noticias: https://www.cbnavalcarnero.es/web/noticias.aspx
- Club Baloncesto Navalcarnero club: https://www.cbnavalcarnero.es/web/club.aspx
- Club Baloncesto Navalcarnero equipos: https://www.cbnavalcarnero.es/web/equipos.aspx

## Separacion de hechos, inferencias y recomendaciones

Hechos confirmados:

- La referencia Manchester contiene navegacion, roster, resultados, fixtures, league tables, noticias, sponsors, newsletter y CTA de captacion.
- Navalcarnero publica secciones de club, noticias, resultados, equipos, documentacion y galerias.
- Navalcarnero publica categorias/equipos para temporada 2025/2026.
- Navalcarnero enfoca su proyecto en baloncesto de base y usa instalaciones municipales.

Inferencias:

- Navalcarnero necesitara formularios de inscripcion/prueba y gestion de cantera como prioridad por el contenido actual de noticias.
- La gestion interna probablemente la haran usuarios no tecnicos, por lo que conviene limitar complejidad.
- Publicar jugadores menores exige control de privacidad y autorizaciones.

Recomendaciones:

- Usar WordPress con modelo estructurado propio y ACF.
- Incorporar SportsPress solo si aporta valor real frente a CPTs simples.
- Usar animacion selectiva, no recrear toda la complejidad de Manchester.
- Construir primero un MVP mantenible y ampliar despues.
