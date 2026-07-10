# Ciclo 8 - Preparacion de la tienda WooCommerce (2026-07-07)

Estado: **bloqueado por entregas del cliente**. Este documento fija los
bloqueos, el cuestionario listo para enviar al cliente y el plan de
ejecucion para cuando lleguen las respuestas.

## 1. Bloqueos actuales

| Bloqueo                    | Detalle                                                                                           | Desbloquea             |
| -------------------------- | ------------------------------------------------------------------------------------------------- | ---------------------- |
| Instalacion de WooCommerce | Requiere aprobacion explicita (regla AGENTS.md); usuario dijo "no instalar todavia" el 2026-07-07 | Aprobacion del usuario |
| Catalogo de productos      | El cliente sigue trabajando en el (2026-07-07)                                                    | Entrega del cliente    |
| Metodo de pago provisional | El usuario enviara la pregunta al cliente para que elija                                          | Respuesta del cliente  |

Riesgo de calendario: el plan de Fase 1 situa el ciclo 8 en la semana del
9-16 de julio con presentacion el 2026-07-16. Cada dia sin catalogo
comprime los ciclos 8 y 9 (Redsys test). El plan B de PAYMENTS.md
(tienda visible con transferencia/recogida, tarjeta despues) sigue
disponible y ya fue comunicado al cliente el 2026-07-02.

## 2. Cuestionario para el cliente (listo para enviar)

Texto sugerido, en tono cliente:

---

Hola. Para poder montar la tienda del club esta semana necesitamos tres
cosas. Con esto nos basta para dejarla lista:

**1. Catálogo de productos.** Por cada producto:

- Nombre (ej.: "Camiseta de juego CBN 2026").
- Tallas disponibles (ej.: 6, 8, 10, 12, S, M, L, XL).
- Precio por talla si varía (IVA incluido).
- Unidades en stock por talla (aproximado vale).
- Foto del producto (la mejor que tengáis; podemos retocarla).
- Personalización si aplica (nombre/dorsal) y su suplemento.

Vale una tabla, un Excel o incluso fotos con los datos apuntados.

**2. Cómo cobramos mientras el banco activa el TPV.** El pago con
tarjeta llegará cuando el banco entregue el alta del TPV virtual.
Mientras tanto, elegid una opción:

- Opción A: tienda funcional con pago por transferencia bancaria y/o
  recogida y pago en el club (recomendada: la tienda vende desde el
  primer día).
- Opción B: tienda solo como escaparate (se ven los productos pero no
  se compra hasta que esté el pago con tarjeta).

Si elegís la opción A, necesitamos el IBAN de la cuenta del club para
las transferencias y el punto/horario de recogida.

**3. Responsable de pedidos.** Quién del club revisará los pedidos,
gestionará entregas/recogidas y cambios de talla. Nombre y email.

---

## 3. Decisiones tecnicas ya tomadas (no dependen del cliente)

- WooCommerce se instala via wp-cli en el entorno Docker de QA y en el
  hosting final; su codigo NO se versiona en este repositorio. Se
  documenta la version instalada en el changelog y en PAYMENTS.md §11.
- La integracion propia (plantillas de tienda del tema, CSS 60/30/10,
  hooks) SI se versiona en `wp-content/themes/cbn-theme/`.
- Sin pasarela de tarjeta en el ciclo 8: transferencia (BACS) y/o
  recogida local nativas de WooCommerce, segun elija el cliente.
  Redsys queda integro para el ciclo 9 con el entorno de test estandar
  (PAYMENTS.md §2 y §5).
- Los productos que entregue el cliente se cargan por wp-cli/CSV; si el
  catalogo se retrasa mas alla del 2026-07-10, se montara la tienda con
  4-6 productos sinteticos de merchandising para no bloquear el ciclo 9,
  y se sustituiran al llegar el catalogo real (cambio de datos, no de
  codigo).

## 4. Plan de ejecucion al desbloquear

1. Aprobacion de instalacion → instalar WooCommerce en Docker QA
   (wp-cli), configuracion base: moneda EUR, pais ES, IVA, paginas de
   tienda/carrito/checkout/mi-cuenta.
2. Cargar catalogo (real o sintetico segun §3).
3. Plantillas del tema para tienda/producto/carrito/checkout alineadas
   al sistema visual 60/30/10 existente.
4. Metodo de pago provisional segun respuesta del cliente.
5. QA visual desktop/mobile con capturas (regla AGENTS.md) + flujo de
   pedido completo en Docker.
6. PR con el flujo multiagente habitual; merge solo con confirmacion
   explicita del usuario.
7. Ciclo 9: plugin Redsys + matriz de pruebas de PAYMENTS.md §5.
