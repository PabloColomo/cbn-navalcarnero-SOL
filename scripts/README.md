# Scripts de ayuda local

Los scripts PowerShell se ejecutan desde la raiz del repositorio CBN Sol.

## Arranque completo recomendado

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\bootstrap-local.ps1
```

`bootstrap-local.ps1` no cambia de rama ni descarga bases de datos privadas. De
forma idempotente:

- crea `.env` desde `.env.example` si hace falta;
- ejecuta `npm ci` y `npm run build`;
- levanta WordPress y MariaDB;
- instala WordPress solo si esta vacio;
- activa `cbn-theme`;
- crea las paginas esenciales y configura portada, noticias y permalinks;
- mantiene intactos usuarios y contenidos cuando WordPress ya estaba instalado.

Para omitir el build en una comprobacion local rapida:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\bootstrap-local.ps1 -SkipBuild
```

Sin un manifest vigente, el tema carga los CSS fuente versionados, de modo que
`-SkipBuild` no elimina los estilos Pista Viva. El build completo sigue siendo
obligatorio para validacion y para cargar el bundle de animaciones en
staging/produccion.

## Scripts parciales

- `start-local.ps1`: ejecuta `npm ci` y `npm run build` con Node.js 22+.
- `start-docker.ps1`: arranca Compose y espera a que MariaDB este healthy.
- `import-db.ps1`: importa el dump local opcional `cbn-db-export-2026-07-09.sql`.
- `restore-uploads.ps1`: restaura manualmente un ZIP local de uploads.

Los dumps, ZIP, uploads, `.env`, contrasenas y datos de produccion no deben
subirse al repositorio.
