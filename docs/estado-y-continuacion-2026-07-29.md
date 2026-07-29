# Estado del proyecto y cómo continuar

**Fecha:** 2026-07-29
**Para:** cualquier sesión que retome el proyecto sin el contexto de la
conversación anterior, incluido Claude Code desde el móvil sobre el repo de
GitHub.

---

## 1. Dónde está cada cosa

El proyecto tiene **cuatro copias vivas en Docker** en la máquina del usuario.
Es la principal fuente de confusión, así que conviene fijarlo:

| Puerto | Proyecto Docker | Carpeta                                         | Papel                                  |
| ------ | --------------- | ----------------------------------------------- | -------------------------------------- |
| 8086   | `cbnlatest`     | `club naval - GitHub latest`                    | **Referencia visual aprobada.** `main` |
| 8084   | `cbnauditfix`   | `club naval - CBN Sol - correcciones auditoria` | **Rama de trabajo.** Auditoría y fixes |
| 8082   | `cbnpablo`      | `club naval - Pablo CBN Sol`                    | Copia antigua, sin uso                 |
| 8080   | `cbn`           | `club naval`                                    | Worktree de `main`, 8 commits atrasado |

**Decisión del usuario (2026-07-29): el aspecto visual de 8086 es el aprobado y
no debe alterarse.** Se verificó que 8086 y 8084 tienen **código idéntico**
salvo el commit de seguridad `aea0607`, y que ese commit **no cambia el
diseño**: no toca CSS ni assets, y sus dos únicos cambios en plantillas
visibles son `date('Y')` → `wp_date('Y')` y `rel="noreferrer"` →
`rel="noopener noreferrer"`.

> **Aviso para sesiones remotas o móviles.** La base de datos de WordPress vive
> en un volumen Docker por proyecto (`cbnlatest_database-data`, etc.) y **no
> está en Git**. Tampoco los uploads. Una sesión que sólo tenga el repo puede
> leer y escribir código y documentación, pero **no puede** levantar el sitio,
> ver el resultado, ejecutar `scripts/check-security.ps1` ni validar nada
> visualmente. Ese límite ya causó problemas reales: ver la entrada del
> 2026-07-25 en `AGENT_CHANGELOG.md`, donde cuatro tandas de endurecimiento se
> entregaron sin ejecutar y la validación posterior encontró cinco defectos,
> incluido un error fatal en el editor de WordPress.

---

## 2. Estado de la seguridad

Auditoría completa en `docs/auditoria-seguridad-codigo-2026-07-25.md`: **29
hallazgos**, 0 críticos, 6 altos, 10 medios, 13 bajos.

**Cerrados y validados con ejecución real el 2026-07-26:** A-1, A-2, A-3, A-4,
M-1, M-3, M-4, M-9, N-1, N-2, N-4, N-5, C-2, C-6, C-7, C-9, C-10, más la parte
de código de M-2. M-6, M-7 y M-8 quedan documentados en
`docs/despliegue-produccion.md` en lugar de alterar el entorno local.

**Abiertos:** N-3 (textos legales, **bloqueante del TPV**, no técnico),
infraestructura de M-2 (SMTP autenticado + SPF/DKIM/DMARC), M-5 (minimización
de DNI/peso/estatura), C-3, C-4, C-5, C-8, C-11.

Comprobación reproducible, sólo peticiones HTTP de lectura:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\check-security.ps1 -BaseUrl http://localhost:8084
```

Resultado del 2026-07-29: **8084 → 13 correctas / 7 fallos**; **8086 → 7
correctas / 13 fallos**, porque `main` todavía no tiene el endurecimiento.

---

## 3. Alcance de pagos — corregido el 2026-07-29

Los documentos anteriores asumían que el TPV serviría para vender ropa. **No es
así.** El alcance real:

- **Tienda de ropa:** la vende y la cobra **otra empresa**. La web del club
  sólo muestra catálogo y redirige. Sin TPV, sin WooCommerce y sin
  obligaciones de vendedor para esa parte.
- **Inscripciones, cuotas y actividades:** se cobran **en la web del club**.
  Aquí sí hace falta el TPV de Redsys / Banco Sabadell.

**Consecuencia técnica, y es el trabajo que desbloquea todo lo demás:** el
formulario de inscripción **no persiste nada** — envía un correo y termina. Es
incompatible con cobrar: no se puede conciliar un pago contra un correo. Hace
falta un modelo de datos de inscripciones con estado
(`pendiente_de_pago` → `pagada` → `confirmada`). **Ese diseño no depende de
nadie externo y puede empezarse ya.**

Detalle en `PAYMENTS.md` §2 y §7, y en `docs/pasarela-redsys-sabadell.md`.

---

## 4. El camino crítico

```
Textos legales (asesoría) ──┬─> Publicar la web ─> Sabadell revisa ─> Credenciales TPV ─┐
                            │                                                          │
                            └─> Condiciones + cancelación ─────────────────────────────┤
                                                                                       ▼
Modelo de datos de inscripciones ─> Redsys en entorno de pruebas ───────────> Activar cobros
     (empieza ya, no depende de nadie)
```

Dos cosas que suelen malinterpretarse:

1. **Publicar no es una alternativa al TPV: es su requisito.** Sabadell pidió
   el enlace de la web para revisarla. No puede revisar lo que no está
   publicado.
2. **Redsys tiene entorno de pruebas con tarjetas de test.** El circuito
   completo se puede montar y validar sin esperar al banco; cuando lleguen las
   credenciales reales, se cambian en configuración.

Lo que el proyecto necesita de terceros está en
`docs/requisitos-club-2026-07-29.md`, listo para enviar al club.

---

## 5. Trabajo pendiente, y dónde puede hacerse

| Tarea                                          | ¿Sólo con el repo? | Notas                                      |
| ---------------------------------------------- | ------------------ | ------------------------------------------ |
| Diseñar el modelo de datos de inscripciones    | **Sí**             | Documento de diseño. Es lo más prioritario |
| Investigar la federación (¿API o web pública?) | **Sí**             | Investigación web                          |
| Redactar los textos legales                    | —                  | Los hace la asesoría, no el equipo técnico |
| Implementar el modelo de datos                 | No                 | Requiere WordPress corriendo y validación  |
| Integrar Redsys                                | No                 | Requiere entorno de pruebas y ejecución    |
| Listas de jugadores                            | No                 | El CPT `cbn_player` no tiene campos aún    |
| Sustituir imágenes de relleno                  | No                 | Requiere las fotos y validación visual     |
| Cambios en el formulario de inscripción        | No                 | Requiere pruebas de envío reales           |

**Regla práctica:** desde el móvil o en remoto, trabajar lo que se valida
leyendo. Lo que se valida ejecutando necesita la máquina local con Docker.

---

## 6. Estado de Git

- Repo: `https://github.com/PabloColomo/cbn-navalcarnero-SOL` (público).
- `main` = `302fc41`, la referencia visual de 8086. **No tiene el
  endurecimiento de seguridad.**
- `codex/reproducible-cbn-sol` = rama de trabajo, con `aea0607` (endurecimiento
  validado) y este commit de documentación. Empujada a GitHub.
- Fusionar esa rama en `main` lleva la seguridad a la versión aprobada **sin
  cambiar su aspecto**.

`AGENTS.md` exige confirmación humana explícita antes de fusionar. Revisa el
diff antes de aceptar.
