## Scripts de ayuda

Incluye varios scripts PowerShell para facilitar el arranque local en Windows.

- `start-local.ps1` — comprueba `node`/`npm`, propone instalar con `winget`, ejecuta `npm install` + `npm run build`.
- `start-docker.ps1` — ejecuta `docker compose up -d` y espera a que el servicio de base de datos esté `healthy`.
- `import-db.ps1` — importa `cbn-db-export-2026-07-09.sql` usando el servicio `wpcli` del compose.
- `restore-uploads.ps1` — descomprime `uploads-2026-07-09.zip` dentro de `wp-content/uploads/`.

Uso ejemplo (PowerShell, desde la raíz del repo):
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser -Force
.\scripts\start-local.ps1
.\scripts\start-docker.ps1
.\scripts\import-db.ps1
.\scripts\restore-uploads.ps1 -ZipPath .\uploads-2026-07-09.zip
```
