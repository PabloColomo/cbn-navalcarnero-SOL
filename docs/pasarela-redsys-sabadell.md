# Pasarela de pago Redsys / Banco Sabadell — investigación e implementación

**Fecha:** 2026-07-25
**Complementa:** `PAYMENTS.md` (decisión de proveedor, 2026-07-02) y
`docs/auditoria-seguridad-codigo-2026-07-25.md` (hallazgo N-3).

---

## 1. Lo primero: el banco no está evaluando vuestro código

Han pedido el enlace de la web. Lo que revisan en ese paso **no es la calidad
técnica del sitio**, sino que el comercio sea identificable y que las
condiciones de venta estén publicadas. Si una tienda no muestra condiciones
legales, datos del vendedor, política de devoluciones o información clara del
producto, el banco puede retrasar o rechazar el alta.

Es decir: podéis tener el código perfecto y que os denieguen el alta por no
tener publicado el aviso legal. **Ese es hoy vuestro único bloqueo real.**
Ver sección 6 para el guion de lo que debe contener cada página.

---

## 2. Cómo funciona el TPV Virtual de Redsys

Banco Sabadell no tiene pasarela propia: usa **Redsys**, igual que CaixaBank,
Santander, BBVA y la mayoría de la banca española. Eso es una buena noticia —
la integración técnica no depende del banco que elijáis, solo cambian las
credenciales.

### Credenciales que entrega el banco

Al dar de alta el comercio recibiréis:

| Dato                         | Uso                                           |
| ---------------------------- | --------------------------------------------- |
| **FUC** (código de comercio) | Identifica al club ante Redsys                |
| **Número de terminal**       | Normalmente `1` para un único canal de venta  |
| **Clave secreta de firma**   | Firma HMAC SHA256. **Es el secreto crítico.** |
| Acceso al entorno de pruebas | Permite montar y validar todo antes del alta  |

### Flujo, modalidad por redirección

1. El cliente pulsa «Pagar» en la tienda.
2. WooCommerce compone los parámetros del pedido, los serializa a JSON, los
   codifica en Base64 (`Ds_MerchantParameters`) y los firma.
3. El navegador se redirige al TPV Virtual, alojado por Redsys. **Los datos de
   la tarjeta no pasan nunca por vuestro servidor**, que es exactamente lo que
   os quita de encima el grueso del cumplimiento PCI-DSS.
4. El cliente paga, con el 3-D Secure del emisor si aplica.
5. Redsys envía una **notificación online**: un POST HTTP con el resultado
   codificado en UTF-8, dirigido a la URL que hayáis declarado en
   `Ds_Merchant_MerchantURL`.
6. El cliente vuelve a la tienda por la URL de OK o de KO.

### El punto crítico de seguridad

La firma usa **HMAC SHA256**, versión `HMAC_SHA256_V1`, y **es
responsabilidad del comercio validar el HMAC que envía el TPV Virtual** para
garantizar que la respuesta es válida. Se calcula la firma sobre los datos
recibidos y se compara con el `Ds_Signature` del mensaje; si coinciden, el
mensaje se puede procesar.

Traducido: **si no se valida la firma, cualquiera puede enviar un POST a
vuestra URL de notificación diciendo «pedido pagado» y llevarse la mercancía
gratis.** Es el fallo clásico de las integraciones de pago caseras.

Reglas derivadas, ya recogidas en `PAYMENTS.md` §6:

- Validar la firma **siempre**, antes de tocar el estado del pedido.
- Usar el identificador de operación de Redsys para no procesar dos veces la
  misma notificación (Redsys reintenta).
- La notificación online, y no el retorno del navegador, es la fuente de
  verdad. El cliente puede cerrar la pestaña antes de volver.
- No registrar nunca la clave secreta ni el payload íntegro en los logs.

---

## 3. Qué plugin usar

**No implementéis el protocolo a mano.** Firmar, validar y gestionar
reintentos y devoluciones ya está resuelto, y un error aquí se paga en dinero.

Las librerías de Redsys en PHP, Java y .NET son opcionales pero simplifican el
desarrollo; aun así, para WooCommerce lo sensato es un plugin.

| Opción                                                                                                                             | Valoración                                                                                                                                                                                                                                |
| ---------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Redsys Gateway de José Conti** (`woo-redsys-gateway-light` en WordPress.org, versión gratuita; premium en plugins.joseconti.com) | **Recomendado.** Es el mismo autor del plugin oficial del catálogo de WooCommerce.com. Mantenimiento muy activo: versiones publicadas en enero, febrero y marzo de 2026. La versión gratuita cubre el caso de una tienda de ropa de club. |
| Plugin oficial de WooCommerce.com                                                                                                  | Mismo autor. Añade cosas que no necesitáis todavía (pago por referencia, numeración consecutiva de facturas, exportaciones).                                                                                                              |
| Otros comerciales (modulosdepago.es y similares)                                                                                   | Alternativa válida. Sin ventaja clara para este caso.                                                                                                                                                                                     |
| Integración propia                                                                                                                 | **Desaconsejado.** Asumís la validación de firma, idempotencia, devoluciones y Bizum sin ganar nada.                                                                                                                                      |

**Bizum:** merece la pena preguntarlo al banco en la misma gestión. Para un
club local es un método de pago muy usado y se contrata sobre el mismo TPV.

---

## 4. Impacto en la seguridad del sitio

Instalar WooCommerce multiplica la superficie. Tres cosas concretas de este
proyecto que hay que revisar **antes**:

1. **La lista blanca de capabilities.** `cbn_get_non_admin_allowed_capabilities()`
   en `access-control.php` reduce a `read` toda cuenta sin `manage_options`.
   Los clientes de WooCommerce tienen el rol `customer`. Habrá que ampliarla
   con el filtro `cbn_non_admin_allowed_capabilities`, de forma mínima y
   explícita.
2. **`cbn_restrict_non_admin_dashboard()`** expulsa de wp-admin a quien no sea
   administrador. WooCommerce usa páginas de «Mi cuenta» en el frontend, así
   que no debería chocar, pero hay que probarlo.
3. **`cbn_disable_comment_support()`** retira los comentarios de todos los
   tipos públicos. Si algún día queréis reseñas de producto, habrá que
   exceptuar `product`.

Recomendación fuerte: **checkout como invitado, sin cuentas de cliente.**
Menos datos personales que custodiar, menos superficie, menos obligaciones
RGPD. Para vender camisetas a familias del club no hace falta que nadie se
registre.

---

## 5. Plan por fases

| Fase | Qué                                                            | Bloqueado por                               |
| ---- | -------------------------------------------------------------- | ------------------------------------------- |
| 0    | **Publicar los textos legales** (sección 6)                    | Asesoría del club                           |
| 1    | Resto del endurecimiento: HTTPS, HSTS, SMTP, copias            | Hosting                                     |
| 2    | Pedir el alta del TPV al Sabadell y las credenciales de prueba | Banco                                       |
| 3    | Instalar WooCommerce en staging + revisar el punto 4           | Aprobación explícita (regla de `AGENTS.md`) |
| 4    | Catálogo con los productos reales                              | Cliente                                     |
| 5    | Redsys en modo test + matriz de pruebas de `PAYMENTS.md` §5    | Fase 2                                      |
| 6    | Revisión legal y operativa; asignar responsable de pedidos     | Club                                        |
| 7    | Cambio a producción con la checklist de `PAYMENTS.md` §7       | Todo lo anterior                            |

El plan B de `PAYMENTS.md` sigue vigente y es el más realista para llegar a
tiempo: **tienda funcionando con transferencia bancaria y recogida en el club**,
y la tarjeta se activa cuando el banco entregue el TPV. Vender desde el primer
día no depende de Redsys.

---

## 6. Guion de los textos legales

**Esto no lo puede redactar el equipo técnico ni un agente.** Son documentos
con efectos jurídicos y el responsable del tratamiento es el club. Lo que sigue
es el guion para que la asesoría los produzca rápido, con los datos que ya
están verificados en `docs/clubs/club-baloncesto-navalcarnero.md`.

Datos confirmados disponibles: **Club Baloncesto Navalcarnero**, CIF
**G-80115298**, C/ Río Ebro s/n, Pabellón Municipal La Estación, 28600
Navalcarnero (Madrid), teléfono (+34) 696 849 235.

### `/aviso-legal/`

- Denominación oficial, CIF y domicilio social.
- Datos de inscripción en el Registro de Entidades Deportivas.
- Correo y teléfono de contacto.
- Titularidad del dominio y del sitio.
- Condiciones de uso y propiedad intelectual de textos e imágenes.

### `/privacidad/`

- Responsable del tratamiento y forma de contacto.
- Finalidades: gestión de inscripciones, contacto, pedidos de tienda.
- Base jurídica de cada finalidad.
- **Tratamiento de datos de menores y consentimiento del tutor legal.** Es la
  parte que más cuidado exige, dado el formulario de inscripción.
- Plazos de conservación. Hoy no existen; hay que fijarlos.
- Destinatarios: pasarela de pago, hosting, proveedor de correo.
- Derechos de acceso, rectificación, supresión, oposición y portabilidad.
- Reclamación ante la AEPD.

### `/cookies/`

- Cookies propias y de terceros que se usan realmente. Hoy el sitio usa muy
  pocas; WooCommerce añadirá las de carrito y sesión.
- Finalidad y duración de cada una.
- Cómo revocar el consentimiento.

### `/condiciones-de-compra/`

- Identificación del vendedor.
- Proceso de compra y confirmación del pedido.
- Precios con IVA incluido y gastos de envío.
- Medios de pago aceptados.
- Plazos de entrega o condiciones de recogida en el club.
- Facturación.

### `/devoluciones/`

- Derecho de desistimiento de 14 días y sus excepciones. **Ojo con las
  prendas personalizadas con nombre y dorsal: suelen quedar excluidas, y hay
  que decirlo expresamente.**
- Procedimiento y plazos de reembolso.
- Quién asume los gastos de devolución.
- Cambios de talla.

### Además

- Enlazar las cinco páginas desde el pie, visibles en todas las secciones. Hoy
  el pie ya enlaza privacidad y aviso legal.
- Casilla de aceptación de las condiciones en el checkout, **sin premarcar**.
- Revisar el texto de consentimiento de los dos formularios actuales, que hoy
  dice literalmente «texto legal definitivo pendiente de aprobación».

---

## Fuentes

- [Redsys · Desarrolladores TPV Virtual](https://pagosonline.redsys.es/)
- [Redsys · Firmar una operación](https://pagosonline.redsys.es/desarrolladores-inicio/documentacion-operativa/firmar-una-operacion/)
- [Redsys · Devolver o anular un pago](https://pagosonline.redsys.es/desarrolladores-inicio/documentacion-operativa/devolver-o-anular-un-pago/)
- [Guía de migración a firma HMAC SHA256 — canal Banco Sabadell](https://canales.redsys.es/canales/bsabadell/ayuda/documentacion/Guia%20migracion%20a%20HMAC%20SHA256%20-%20conexion%20por%20redireccionmodificado%20bs.pdf)
- [Manual de integración por redirección — canal Banco Sabadell](https://canales.redsys.es/canales/bsabadell/ayuda/documentacion/Manual%20integracion%20para%20conexion%20por%20Redireccionmodificado%20bs.pdf)
- [Payment Gateway for Redsys & WooCommerce Lite (WordPress.org)](https://wordpress.org/plugins/woo-redsys-gateway-light/)
- [Plugins de Redsys para WooCommerce — José Conti](https://plugins.joseconti.com/en/)
- [WooCommerce Redsys Gateway 30.3.x (marzo 2026)](https://plugins.joseconti.com/en/2026/03/07/woocommerce-redsys-gateway-303x/)
- [Pasarela Redsys para WooCommerce — WooCommerce.com](https://woocommerce.com/products/redsys-gateway/)
- [Cómo conseguir credenciales del TPV virtual (Redsys)](https://pagosrecurrentes.com/blog/como-conseguir-credenciales-tpv-virtual-redsys)
- [Cómo solicitar un TPV virtual paso a paso](https://finantres.com/solicitar-tpv-virtual/)
