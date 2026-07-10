# Setup inicial del proyecto

Este setup deja el repositorio preparado para iniciar la web nueva del Club Baloncesto Navalcarnero con una base WordPress mantenible.

## Objetivo

Crear una base tecnica que permita pasar del plan de rediseno a desarrollo real sin bloquear al club en una solucion fragil o dificil de mantener.

## Decisiones iniciales

- WordPress se usa como CMS porque el club necesitara editar noticias, equipos, partidos, documentos, sponsors, productos e inscripciones sin depender de desarrollo para cada cambio.
- El tema custom `cbn-theme` contiene la presentacion visual.
- El plugin MU `cbn-core` contiene los tipos de contenido y taxonomias para que los datos del club no dependan del tema.
- Vite compila los assets frontend del tema.
- Docker Compose ofrece un entorno local reproducible con WordPress, MariaDB, phpMyAdmin opcional y WP-CLI.
- La direccion visual debe respetar una proporcion global de 60% blanco, 30% rojo y 10% negro.
- Los permisos de imagen y datos han sido confirmados por el responsable del proyecto; aun asi, la web debe mantener controles de privacidad y visibilidad.
- WooCommerce se usara como base recomendada para tienda de ropa, pedidos e inscripciones con pago.
- Stripe se usara como pasarela principal recomendada, validando primero en modo test.

## Primer MVP tecnico

El primer MVP debe poder cubrir:

- Home basica del club.
- Pagina de noticias.
- Equipos por temporada/categoria.
- Partidos y resultados basicos.
- Sponsors.
- Documentos.
- Contacto e inscripciones mediante formularios.
- Tienda de ropa del club con productos simples o variables.
- Pago online para ropa e inscripciones.
- Confirmaciones por email y avisos internos al club.

## Pagos

En WordPress, el enfoque recomendado es:

- WooCommerce para catalogo, carrito, pedidos, emails transaccionales, cupones e inventario basico.
- Extension oficial de Stripe para WooCommerce como pasarela principal.
- Checkout alojado o componentes oficiales para no manejar datos de tarjeta en el servidor del club.
- Webhooks configurados antes de produccion.
- SSL obligatorio en produccion.
- Pruebas de pago correcto, pago fallido, cancelacion, reembolso y emails.

Para inscripciones, hay dos caminos posibles:

- Opcion simple: productos WooCommerce virtuales para inscripciones, pruebas, campus o cuotas puntuales.
- Opcion avanzada: formulario de inscripcion dedicado que cree pedidos WooCommerce/Stripe con metadatos completos del jugador, tutor, temporada, categoria y consentimientos.

## Comandos

```bash
cp .env.example .env
docker compose up -d
npm install
npm run build
```

Documentacion detallada del entorno local: `docs/LOCAL_DOCKER.md`.

WordPress local:

```text
http://localhost:8080
```

phpMyAdmin local:

```text
http://localhost:8081
```

phpMyAdmin se levanta con el perfil de herramientas:

```bash
docker compose --profile tools up -d
```

WP-CLI local:

```bash
docker compose run --rm wpcli wp --info
```

## Pendientes antes de desarrollo visual

- Confirmar si se usara GeneratePress/Bricks o si el tema custom sera completamente propio.
- Confirmar plugin de formularios: Fluent Forms, Gravity Forms o integracion con WooCommerce para formularios de inscripcion con pago.
- Confirmar si SportsPress entra en el MVP o si se mantiene un modelo propio de partidos/resultados.
- Conseguir escudo vectorial, paleta exacta dentro del marco 60/30/10 y fotografias autorizadas.
- Definir quien actualizara equipos, partidos, noticias, productos, pedidos e inscripciones.
- Confirmar si Stripe sera suficiente o si tambien se requiere TPV bancario/Redsys.
- Definir politica de recogida/envio de ropa, devoluciones, condiciones de compra y condiciones de inscripcion.
