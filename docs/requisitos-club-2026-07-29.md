# Requisitos pendientes del club — textos legales, materiales y decisiones

**Fecha:** 2026-07-29
**Estado:** pendiente de enviar al club
**Complementa:** `PAYMENTS.md`, `docs/pasarela-redsys-sabadell.md`,
`docs/auditoria-seguridad-codigo-2026-07-25.md` (hallazgo N-3)

Este documento es el mensaje a enviar al club. Recoge todo lo que el proyecto
necesita de terceros para poder publicarse y, después, cobrar inscripciones.

## Escenario confirmado

- **Tienda de ropa:** catálogo con fotos y precios en la web del club; al
  comprar se redirige a la web de **otra empresa**, que vende y cobra.
- **Inscripciones, cuotas y actividades:** se gestionan y **se cobran en la
  propia web del club**.

---

## Parte A — Textos legales

Los redacta la asesoría del club. No los redacta el equipo técnico ni un
agente: son documentos con efectos jurídicos y el responsable del tratamiento
es el club.

Datos ya verificados, disponibles en `docs/clubs/club-baloncesto-navalcarnero.md`:

| Dato         | Valor                                                                        |
| ------------ | ---------------------------------------------------------------------------- |
| Denominación | Club Baloncesto Navalcarnero                                                 |
| CIF          | G-80115298                                                                   |
| Domicilio    | C/ Río Ebro s/n, Pabellón Municipal La Estación, 28600 Navalcarnero (Madrid) |
| Teléfono     | (+34) 696 849 235                                                            |
| Correo       | administracion@cbnavalcarnero.es                                             |

**Dato que falta:** inscripción en el **Registro de Entidades Deportivas de la
Comunidad de Madrid** (número y fecha). La web antigua del club publica ese
apartado con valores de plantilla («x»), así que no se ha podido recuperar. El
aviso legal lo necesita.

### 1. Aviso legal

Denominación oficial, CIF y domicilio · datos de inscripción registral ·
correo y teléfono · titularidad del dominio y del sitio · condiciones de uso y
propiedad intelectual de textos e imágenes.

### 2. Política de privacidad

La más delicada: la web trata **datos de menores** y, al activar los cobros,
también datos de pago.

- Responsable del tratamiento y forma de contacto.
- Finalidades: gestión y cobro de inscripciones, cuotas y actividades;
  atención de consultas.
- Base jurídica de cada finalidad.
- Tratamiento de datos de menores y consentimiento del tutor legal.
- Plazos de conservación. **Hoy no existen y hay que fijarlos**, incluidas las
  obligaciones fiscales de conservación de una inscripción pagada.
- Destinatarios: hosting, proveedor de correo, pasarela de pago (Redsys /
  Banco Sabadell) y la empresa que gestiona la tienda de ropa.
- Derechos de acceso, rectificación, supresión, oposición, limitación y
  portabilidad, y reclamación ante la AEPD.

Datos que recogen hoy los formularios, verificados en
`wp-content/themes/cbn-theme/inc/registration-content.php`:

| Formulario                    | Datos recogidos                                                 |
| ----------------------------- | --------------------------------------------------------------- |
| Inscripción — jugador/a       | Nombre completo, fecha de nacimiento, sexo, DNI, estatura, peso |
| Inscripción — tutor           | Nombre completo, teléfono, correo, relación con el jugador/a    |
| Inscripción — solicitante     | Teléfono y correo de contacto                                   |
| Inscripción — consentimientos | Aceptación RGPD y declaración del tutor legal                   |
| Contacto                      | Nombre, correo, teléfono y mensaje                              |

Dos puntos a valorar expresamente:

- **Hoy ninguna solicitud se guarda en base de datos**, sólo se envía por
  correo. Al empezar a cobrar eso tiene que cambiar. La política debe reflejar
  el nuevo tratamiento, y el club debe fijar quién accede y durante cuánto.
- **¿Son necesarios el DNI, la estatura y el peso en la solicitud web?**
  Recomendación técnica: pedirlos sólo al confirmar plaza, por canal
  controlado. Es decisión del club (hallazgo **M-5** de la auditoría).

### 3. Política de cookies

Cookies propias y de terceros realmente usadas, finalidad y duración, y cómo
revocar el consentimiento. La pasarela añadirá las suyas.

**Decisión necesaria:** ¿se quiere analítica tipo Google Analytics? Si no, la
política se simplifica mucho y probablemente no haga falta banner.

### 4. Condiciones de contratación de inscripciones y cuotas

Sustituye a lo que en una tienda serían «condiciones de compra». Lo contratado
es un **servicio deportivo**:

- Identificación del club como prestador.
- Qué se contrata: inscripción de temporada, cuota mensual, campus, actividad.
- Precio de cada concepto con impuestos incluidos; importe y calendario de las
  cuotas periódicas.
- Proceso de contratación y confirmación.
- Medios de pago aceptados.
- Duración del servicio y forma de renovación.
- Consecuencias del impago de una cuota.
- Facturación y datos fiscales.
- Requisitos de admisión, si los hay.

### 5. Política de cancelación y devolución

La asesoría debe pronunciarse específicamente sobre:

- **Derecho de desistimiento de 14 días en un servicio que empieza antes de
  que termine ese plazo.** Para poder empezar la actividad de inmediato hace
  falta recoger la renuncia expresa del usuario, en un consentimiento
  **separado** de la aceptación general. Se necesita el texto exacto.
- Cancelación antes de que empiece la temporada.
- Cancelación con la temporada empezada: ¿parte proporcional, sin devolución,
  penalización?
- Baja a mitad de temporada y cuotas ya cobradas.
- Plazos y forma de reembolso.
- Cancelación de una actividad por parte del club.

### 6. Tienda de ropa — sólo identificación del vendedor

El club no necesita condiciones de compra ni devoluciones propias. Sí necesita:

- **Nombre y CIF de la empresa** que vende, para indicarlo en la tienda.
- **Enlace a sus condiciones de compra y devoluciones.**
- Si el club percibe comisión, porque afecta a cómo se describe la relación.

### 7. Textos de consentimiento de los formularios

Hoy muestran literalmente «texto legal definitivo pendiente de aprobación». Se
necesita el texto real y **separado** de cada uno:

- Aceptación de la política de privacidad.
- Declaración del padre, madre o tutor legal.
- Aceptación de las condiciones de inscripción.
- Renuncia expresa al desistimiento (ver punto 5).
- **Consentimiento de uso de imagen**, hoy inexistente y necesario para
  publicar fotos de jugadores.
- Comunicaciones comerciales, si se quieren: opcional y nunca premarcado.

---

## Parte B — Decisiones y materiales

### 1. Alta del TPV con Banco Sabadell

Del banco se necesita: código de comercio (FUC), número de terminal, clave
secreta de firma y **acceso al entorno de pruebas** — esto último es lo
primero, porque permite montar y validar todo antes de cerrar el alta.

El banco **revisará la web antes de dar el alta**, y comprueba que el club sea
identificable y las condiciones estén publicadas: la Parte A es requisito
previo. Conviene preguntar también por **Bizum**, que se contrata sobre el
mismo TPV.

### 2. Conceptos que se cobran

Listado completo con importe y periodicidad. Sin esto no se puede construir la
pantalla de pago.

### 3. Catálogo de la tienda

Nombre, descripción, tallas y precio de cada producto, **fotografía de cada
uno**, y el enlace de destino en la web de la otra empresa.

### 4. Listas de jugadores

Propuesta: publicar **dorsal + nombre de pila + posición**, sin apellidos. Se
necesita confirmación del formato, el listado por equipo, confirmación de que
existe **consentimiento del tutor legal** para cada menor, y un procedimiento
para **retirar a un jugador** si una familia lo pide.

### 5. Cambios en el formulario de inscripción

Qué campos añadir, quitar o modificar · decisión sobre DNI, estatura y peso ·
correo de destino de las solicitudes · **persona responsable de gestionarlas y
de conciliar los pagos**.

### 6. Fotografías del club

Selección de fotos indicando sección de destino, y **confirmación de
consentimiento de imagen** de quienes aparezcan, especialmente menores. Hoy hay
imágenes de relleno en varias secciones.

### 7. Resultados en directo desde la federación

Se necesita saber con qué federación y competiciones se compite, si el club
tiene **acceso a algún sistema, API o exportación**, y si hay **autorización de
la federación** para reutilizar esos datos. Sin acceso oficial, la alternativa
es introducir resultados a mano desde el panel, que ya está preparado.

---

## Qué bloquea qué

| Necesitamos                               | Bloquea                                    |
| ----------------------------------------- | ------------------------------------------ |
| Aviso legal, privacidad y cookies         | **La publicación de la web**               |
| Condiciones de contratación y cancelación | **El alta del TPV y cobrar inscripciones** |
| Datos de inscripción registral            | El aviso legal                             |
| Textos de consentimiento                  | Que los formularios se puedan usar         |
| Credenciales de prueba del TPV            | Montar y validar el pago                   |
| Conceptos y precios                       | La pantalla de pago                        |
| Catálogo, enlaces y datos del vendedor    | La sección de tienda                       |
| Listas de jugadores y consentimientos     | La sección de equipos                      |
| Fotos seleccionadas                       | Retirar las imágenes de relleno            |
| Acceso a la federación                    | Los resultados automáticos                 |

La web puede publicarse con los **tres primeros documentos legales**, con la
tienda como catálogo y las inscripciones sin pago online. Los cobros se activan
después. No hace falta esperar a tenerlo todo.
