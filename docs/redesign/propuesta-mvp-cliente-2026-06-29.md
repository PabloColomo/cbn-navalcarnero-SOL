# Propuesta MVP para la nueva web del Club Baloncesto Navalcarnero

Fecha: 2026-06-29

## Estado (actualizado 2026-07-02)

- **Fase 1 aceptada por el cliente** con plazo de ~2 semanas (objetivo
  2026-07-16). Plan operativo: `docs/redesign/fase1-plan-2026-07-02.md`.
- **Pasarela resuelta**: TPV virtual bancario (= Redsys). Pendiente solo la
  confirmacion del banco (Sabadell o Ibercaja); ambos usan Redsys. Ver
  `PAYMENTS.md`.
- **Informacion solicitada al club** por chat el 2026-07-02 (seccion 8,
  excepto pagos y tienda, que van por otras vias).
- **Proveedor textil contactado** el 2026-07-02; pendiente de respuesta
  para catalogo de tienda.

## 1. Objetivo del informe

Este informe resume lo que Isabel y Leopoldo han trasladado en el formulario de alcance y lo convierte en una propuesta inicial de trabajo para la nueva web del Club Baloncesto Navalcarnero.

La idea es avanzar con una primera version realista, util y presentable en un plazo corto, sin perder de vista las funcionalidades mas ambiciosas que se han pedido para fases posteriores.

## 2. Lo que ambos han pedido en comun

De las dos respuestas se ve una direccion compartida:

- Modernizar la imagen del club.
- Crear una web clara, ordenada y facil de usar.
- Informar mejor a familias, jugadores y personas interesadas.
- Facilitar inscripciones y tramites.
- Incluir tienda o venta de ropa/productos del club.
- Incorporar pago online.
- Dar mas presencia a patrocinadores.
- Mostrar informacion deportiva: equipos, partidos, resultados y datos conectados con federacion cuando sea posible.
- Mantener una imagen visual limpia con los colores del club.
- Preparar una web que pueda crecer despues sin rehacerla desde cero.

## 3. Puntos que hay que poner en comun

Hay algunos temas donde las respuestas no son exactamente iguales. Conviene cerrarlos cuanto antes para evitar cambios de rumbo durante el desarrollo.

### Fecha objetivo

- Isabel indica un plazo aproximado de 15 dias.
- Leopoldo indica como referencia el 1 de septiembre.

Decision necesaria: definir si se quiere una primera version urgente y limitada, o una version mas completa para septiembre.

### Prioridad principal de la web

- Isabel da mas peso a noticias, inscripciones, tienda y pago.
- Leopoldo da mas peso a informacion institucional, equipos, partidos, resultados, documentacion y galerias.

Decision necesaria: acordar que bloques son imprescindibles en la primera entrega y cuales pueden entrar en una segunda fase.

### Informacion de jugadores y menores

- Isabel propone mostrar informacion muy limitada, como nombre de pila o numero de equipacion.
- Leopoldo indica que se publique solo lo autorizado legalmente por las familias.

Decision necesaria: definir una politica clara de privacidad para jugadores, especialmente menores.

### Pagos y pasarela

- Isabel propone Redsys / TPV bancario.
- Leopoldo lo deja pendiente de decidir.

Decision necesaria: confirmar si la pasarela sera Redsys/TPV, Stripe u otra opcion. La pasarela debe configurarse primero en modo test.

### Gestion posterior

- Isabel no ve necesaria formacion.
- Leopoldo si contempla formacion o manual de WordPress.

Decision recomendada: preparar al menos una guia breve de gestion para contenidos, inscripciones, tienda y noticias.

## 4. Propuesta de composicion de la web

### Navegacion principal

La navegacion recomendada para la primera version seria:

- Inicio
- El Club
- Equipos
- Partidos / Resultados
- Noticias
- Inscripciones
- Tienda
- Sponsors
- Contacto

Documentacion y Galerias pueden aparecer tambien, pero se recomienda incorporarlas si hay contenido suficiente y bien organizado.

### Home

La pagina de inicio debe funcionar como resumen claro de todo el club. Propuesta de bloques:

1. Cabecera con escudo, menu principal y llamada a la accion.
2. Hero principal con mensaje del club y botones hacia inscripcion, equipos o tienda.
3. Accesos rapidos para familias: inscripciones, equipos, partidos, contacto.
4. Bloque de proximo partido, resultados recientes o acceso a federacion.
5. Bloque de equipos/cantera.
6. Noticias y comunicados recientes.
7. Bloque de inscripciones.
8. Bloque de tienda.
9. Sponsors y colaboradores.
10. Footer con datos de contacto, redes, enlaces legales y patrocinadores principales.

### Pagina "El Club"

Debe explicar quienes son, que hacen y que ofrece el club, evitando textos largos y poco visuales. Propuesta:

- Presentacion breve.
- Valores del club.
- Instalaciones.
- Escuela/cantera.
- Datos destacados del club.
- Enlace a contacto e inscripciones.

### Equipos

La seccion de equipos debe poder crecer con el tiempo. Para el MVP:

- Listado de equipos/categorias.
- Informacion general de cada equipo.
- Horarios o datos basicos si estan disponibles.
- Acceso a partidos/resultados si existe la informacion.

Las fichas completas de jugadores se pueden dejar para una fase posterior o activarse solo con datos autorizados.

### Partidos y resultados

Esta seccion debe mostrar:

- Proximos partidos.
- Resultados recientes.
- Equipo local/visitante.
- Fecha, hora e instalacion.
- Competicion/jornada si la federacion lo facilita.

Si la integracion con federacion no esta lista en la primera semana, la web debe permitir carga manual o importacion inicial sencilla.

### Inscripciones

Debe ser una seccion prioritaria. Debe incluir:

- Explicacion clara del proceso.
- Formulario o acceso a formulario.
- Datos del jugador.
- Datos del padre/madre/tutor cuando proceda.
- Consentimientos legales.
- Posibilidad de pago asociado cuando corresponda.

Campos mencionados por el club:

- Nombre y apellidos.
- DNI si aplica.
- Fecha de nacimiento.
- Sexo.
- Telefono.
- Email.
- Datos del progenitor/tutor.
- Datos adicionales si el club los requiere, como estatura o peso.

### Tienda

La tienda debe cubrir inicialmente productos basicos:

- Camisetas.
- Pantalones.
- Camisetas de entrenamiento.
- Camisetas de calentamiento.
- Sudaderas.

Opciones solicitadas:

- Tallas.
- Recogida local.
- Envio a domicilio.
- Emails automaticos.
- Cambios/devoluciones.

La gestion concreta de stock, precios, tallas y proveedor textil debe confirmarse antes de configurarla.

### Sponsors

Los patrocinadores no deben quedar solo en el pie de pagina. Propuesta:

- Bloque visible en home.
- Pagina propia de sponsors.
- Jerarquia por nivel de patrocinio.
- Logo, nombre, enlace y posible descripcion.
- Espacio para patrocinadores principales o institucionales.

### Contacto

Debe ser directo y facil:

- Formulario de contacto.
- Email o telefono publico si se autoriza.
- Ubicaciones/instalaciones.
- Enlaces a redes sociales.

## 5. Estilo visual propuesto

La direccion visual debe equilibrar las dos preferencias recogidas:

- Isabel: deportivo, claro y mantenible.
- Leopoldo: institucional, limpio y funcional.

Propuesta final:

Una web deportiva, limpia e institucional, con energia visual pero sin desorden. Debe transmitir club activo, serio y cercano para familias.

Reglas visuales:

- 60% blanco como superficie dominante.
- 30% rojo como color de marca, llamadas a la accion, bandas y estados destacados.
- 10% negro para texto, contraste, cabecera/footer y estructura.
- Evitar mezclas de colores sin funcion clara.
- Mobile-first: la web debe funcionar especialmente bien en telefono.
- Tipografia clara y legible.
- Botones y llamadas a la accion faciles de identificar.
- Fotografias reales del club cuando esten seleccionadas y autorizadas.

## 6. Funcionalidades solicitadas

### Funcionalidades base del MVP

- Home moderna.
- Pagina del club.
- Equipos.
- Partidos/resultados.
- Noticias/comunicados.
- Inscripciones.
- Contacto.
- Sponsors.
- Tienda con productos reales.
- Pago online completo.
- Preparacion para datos de federacion.
- Galerias/documentacion si el contenido esta listo.

### Pasarela de pagos

El pago online es un requisito importante. Se debe contemplar para:

- Tienda de ropa.
- Inscripciones.
- Campus o actividades.
- Cuotas/eventos si el club lo decide.

Condiciones para activarlo correctamente:

- Elegir pasarela: Redsys/TPV, Stripe u otra.
- Configurar primero en modo test.
- Tener SSL activo en produccion.
- Revisar emails transaccionales.
- Definir politica de devoluciones/cambios.
- Confirmar condiciones legales y fiscales.
- Asignar quien gestionara pedidos, pagos y conciliacion.

### Federacion: equipos, jugadores, partidos, resultados y estadisticas

El club ha pedido conectar la web con informacion de la federacion. Datos esperados:

- Equipos.
- Jugadores.
- Calendario de partidos.
- Resultados.
- Clasificaciones.
- Estadisticas.
- Competiciones.
- Temporadas.

Condicion tecnica:

Antes de prometer una importacion automatica completa hay que saber como se puede acceder a esos datos:

- API oficial.
- CSV/Excel.
- Panel privado con exportaciones.
- Paginas publicas.
- Otro sistema autorizado.

Propuesta para el MVP:

- Preparar la estructura de WordPress para guardar esos datos.
- Mostrar equipos, partidos y resultados de forma clara.
- Permitir carga manual o importacion inicial si no hay acceso federativo listo.
- Dejar preparada la integracion automatica para una fase posterior si requiere mas analisis.

No se deben guardar credenciales de federacion en el repositorio ni compartirlas por chat.

### Noticias y comunicados

La web debe permitir publicar:

- Noticias del club.
- Comunicados importantes.
- Informacion de temporada.
- Campus o eventos.
- Avisos de inscripcion.
- Cronicas si el club quiere mantenerlas.

### Gestion interna

La web debe ser mantenible por el club. Se recomienda:

- Roles de usuario claros.
- Guia breve de uso.
- Flujo sencillo para publicar noticias, equipos, productos e inscripciones.
- Evitar depender del desarrollador para cambios cotidianos.

## 7. Alcance realista para una primera semana

Para una entrega rapida, se recomienda separar entre "entregable MVP" y "preparado para evolucion".

### Debe entrar en la primera version

- Estructura visual principal.
- Home.
- Paginas base.
- Equipos basicos.
- Noticias/comunicados.
- Sponsors.
- Inscripciones basicas.
- Tienda con productos reales definidos por el club.
- Pagos completos para tienda e inscripciones, configurados primero en modo test.
- Preparacion tecnica para datos deportivos.
- Responsive movil.

### Puede entrar si se entregan accesos y contenido a tiempo

- Formularios definitivos de inscripcion.
- Primeros datos reales de equipos y partidos.
- Importacion inicial desde federacion si el formato es viable.

### Debe quedar para fase posterior

- Portal privado del socio.
- CRM completo.
- Automatizacion avanzada de seguros/listas de espera.
- IA que tramite inscripciones o pagos.
- Integracion federativa automatica completa si no hay API o formato claro.
- Estadisticas avanzadas por jugador si no estan disponibles de forma fiable.

## 8. Informacion necesaria para avanzar sin bloqueos

Para ejecutar bien el MVP, el club debe confirmar o entregar:

- Fecha objetivo final.
- Dominio exacto.
- Hosting y acceso tecnico.
- Si hay correo activo con el dominio.
- Pasarela elegida: Redsys/TPV, Stripe u otra.
- Cuenta o datos necesarios para modo test de la pasarela.
- Logo/escudo definitivo.
- Fotografias autorizadas.
- Lista de equipos/categorias.
- Productos de tienda, tallas, precios y stock inicial.
- Quien gestionara pedidos y devoluciones.
- Campos definitivos de inscripcion.
- Textos legales: privacidad, devoluciones, condiciones de compra/inscripcion.
- Acceso o informacion sobre la fuente de datos federativa.
- Patrocinadores y orden de importancia.

## 9. Propuesta de faseado

### Fase 1: MVP publico

Objetivo: lanzar una web util, clara y presentable.

Incluye:

- Estructura principal.
- Diseno visual.
- Home.
- Club.
- Equipos.
- Noticias.
- Inscripciones.
- Tienda con productos reales.
- Pagos completos.
- Sponsors.
- Contacto.
- Base para federacion y futuras ampliaciones.

### Fase 2: Operativa real

Objetivo: convertir la web en herramienta de gestion.

Incluye:

- Formularios definitivos.
- Emails transaccionales.
- Gestion de pedidos.
- Importacion o sincronizacion inicial con federacion.
- Documentacion y formacion.

### Fase 3: Evolucion avanzada

Objetivo: incorporar funcionalidades diferenciales.

Incluye:

- Area privada.
- Integracion federativa avanzada.
- Estadisticas completas.
- Automatizaciones.
- Asistente IA de consulta.
- CRM o conexion con sistemas internos.

## 10. Conclusion

Las respuestas de Isabel y Leopoldo muestran una direccion comun: una web moderna, ordenada, util para familias y jugadores, con inscripciones, tienda, pagos, patrocinadores y datos deportivos.

La recomendacion es empezar ya con el MVP sobre lo que esta claro, mientras se cierran las decisiones pendientes. Asi se avanza sin perder tiempo y se evita bloquear el proyecto por funcionalidades que necesitan accesos, configuracion legal o validacion tecnica.

La primera version debe ser una base solida, no una solucion improvisada. Debe permitir lanzar rapido, pero tambien crecer hacia pagos completos, federacion, estadisticas y herramientas avanzadas sin rehacer la web.
