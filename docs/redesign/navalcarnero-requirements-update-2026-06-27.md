# Actualizacion de requisitos: color, permisos y pagos

Fecha: 2026-06-27

Esta actualizacion recoge decisiones de alcance posteriores al plan inicial y prevalece sobre cualquier recomendacion anterior que entre en conflicto.

## Direccion cromatica obligatoria

La proporcion de color para la web sera:

- 60% blanco.
- 30% rojo.
- 10% negro.

Aplicacion practica:

- Blanco: superficie dominante, fondos de contenido, formularios, tablas, listados, area editorial y espacios de lectura.
- Rojo CBN: identidad de marca, CTAs, bandas deportivas, estados destacados, enlaces activos, botones principales y bloques de energia.
- Negro: texto principal, header/footer cuando aplique, contraste tipografico, iconografia y elementos estructurales puntuales.

Reglas:

- La interfaz debe leerse mayoritariamente blanca y clara, no oscura.
- El rojo debe tener presencia fuerte pero controlada: no debe impedir la lectura ni saturar formularios o tablas.
- El negro queda reservado para contraste, jerarquia y secciones concretas; no debe dominar la home.
- Amarillo, oro o naranja quedan limitados al escudo, assets oficiales o detalles inevitables de imagenes, no como tokens principales del sistema visual.
- Los tokens CSS, componentes y plantillas deben documentar esta proporcion para mantener consistencia en toda la web.

## Permisos de imagen y datos

El responsable del proyecto confirma que existen permisos para usar imagenes y datos necesarios del club, incluyendo contenido sensible mencionado en el plan inicial.

Aunque los permisos estan confirmados, la implementacion debe seguir manteniendo controles de privacidad:

- Publicar solo los datos aprobados por el club.
- Permitir ocultar jugadores, fotos o datos individuales por equipo/persona.
- Mantener perfiles de menores con informacion limitada y controlable.
- Registrar internamente autorizaciones o confirmaciones cuando el club lo requiera.
- Evitar datos sensibles que no aporten valor publico.

## Pagos y comercio

La web debe incluir pasarela de pago para dos usos principales:

1. Tienda de ropa del club.
2. Inscripciones, pruebas, campus o cuotas/eventos definidos por el club.

### Enfoque recomendado para WordPress

- Usar WooCommerce para catalogo, carrito, pedidos, inventario basico, cupones, emails transaccionales y gestion administrativa.
- Usar Stripe como pasarela principal mediante la extension oficial de Stripe para WooCommerce.
- Para un flujo custom fuera de WooCommerce, usar Stripe Checkout Sessions en lugar de APIs antiguas o formularios de tarjeta propios.
- Mantener los datos de tarjeta fuera del servidor del club usando checkout alojado o componentes oficiales de Stripe/WooCommerce.

### Tienda de ropa

La tienda debe cubrir:

- Productos simples y variables: talla, color, jugador/equipo si procede.
- Stock basico por talla.
- Imagenes de producto.
- Recogida local y, si el club lo decide, envio.
- Pedidos, devoluciones/cambios y emails de confirmacion.
- Posibilidad de productos de temporada o campanas limitadas.

### Inscripciones y pagos del club

Las inscripciones deben cubrir:

- Formulario de datos del jugador/persona inscrita.
- Datos de tutor/responsable cuando aplique.
- Categoria, temporada, campus, prueba o actividad.
- Consentimientos y aceptaciones legales.
- Pago asociado cuando corresponda.
- Confirmacion por email al usuario y aviso interno al club.
- Exportacion o consulta administrativa de inscritos y pagos.

Implementacion posible:

- Opcion simple: productos WooCommerce virtuales para inscripciones/campus, con campos adicionales mediante plugin o desarrollo custom.
- Opcion avanzada: formulario dedicado conectado a WooCommerce/Stripe para crear pedidos con metadatos completos de inscripcion.

### Requisitos operativos de pago

- SSL obligatorio en produccion.
- Modo test antes de publicar.
- Webhooks configurados y verificados.
- Politica visible de devoluciones, privacidad y condiciones de compra/inscripcion.
- Emails transaccionales revisados con identidad del club.
- Roles de administracion limitados para gestion de pedidos.
- Reconciliacion periodica entre pedidos, pagos y altas/inscripciones.
- Confirmar tratamiento fiscal, facturacion y devoluciones con el asesor del club antes de lanzamiento.

## Impacto en el MVP

El MVP ya no debe tratar pagos como futuro opcional. La pasarela de pago entra en el alcance de la primera version si el club necesita lanzar ropa o inscripciones desde la web.

Backlog imprescindible actualizado:

- Sistema visual 60/30/10: blanco, rojo, negro.
- WooCommerce instalado y configurado.
- Stripe configurado en modo test y luego produccion.
- Producto base de ropa del club con variantes.
- Producto o flujo base de inscripcion con pago.
- Emails transaccionales y avisos internos.
- Politicas legales de compra, devolucion, privacidad e inscripcion.
- Pruebas completas de checkout, cancelacion, pago correcto, error de pago y reembolso.

## Decisiones pendientes

- Stripe sera la pasarela principal o se necesita tambien TPV bancario/Redsys?
- La ropa tendra envio, recogida local o ambas opciones?
- Que productos de ropa entran en la primera version?
- Que inscripciones se pagan online en el primer lanzamiento?
- Los pagos de inscripcion son importes unicos, reservas, cuotas recurrentes o combinacion?
- Quien gestionara pedidos, devoluciones y conciliacion de pagos?
- Que textos legales y condiciones aportara el club o su asesor?
