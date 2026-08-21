#!/usr/bin/env bash
# Limpia documentos de la BD de pruebas (enviados/recibidos/despachados + solicitudes).
# Uso (en el servidor, desde la raíz del proyecto SGD):
#   bash scripts/limpiar-documentos-test.sh
#   bash scripts/limpiar-documentos-test.sh --con-archivos

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SQL="$ROOT/scripts/limpiar-documentos-test.sql"
CONTAINER="${SGD_BD_CONTAINER:-sgd_bd}"
DB_USER="${SGD_DB_USER:-sgd_plc}"
DB_NAME="${SGD_DB_NAME:-sgd_plc}"

if [[ ! -f "$SQL" ]]; then
  echo "No se encuentra $SQL"
  exit 1
fi

echo "==> Contando documentos ANTES..."
docker exec -i "$CONTAINER" psql -U "$DB_USER" -d "$DB_NAME" -c \
  "SELECT 'documento' t, count(*) c FROM documento
   UNION ALL SELECT 'documento_buzon', count(*) FROM documento_buzon
   UNION ALL SELECT 'documento_buzon_bitacora', count(*) FROM documento_buzon_bitacora;"

echo "==> Ejecutando limpieza SQL..."
docker exec -i "$CONTAINER" psql -U "$DB_USER" -d "$DB_NAME" < "$SQL"

echo "==> Contando documentos DESPUÉS..."
docker exec -i "$CONTAINER" psql -U "$DB_USER" -d "$DB_NAME" -c \
  "SELECT 'documento' t, count(*) c FROM documento
   UNION ALL SELECT 'documento_buzon', count(*) FROM documento_buzon
   UNION ALL SELECT 'documento_buzon_bitacora', count(*) FROM documento_buzon_bitacora;"

if [[ "${1:-}" == "--con-archivos" ]]; then
  echo "==> Limpiando PDFs en data/files (se conservan imagen_firma y plantillas)..."
  FILES_DIR="$ROOT/data/files"
  if [[ -d "$FILES_DIR" ]]; then
    find "$FILES_DIR" -maxdepth 1 -type f \( -iname '*.pdf' -o -iname '*.PDF' \) -delete
    find "$FILES_DIR" -maxdepth 1 -type f -name 'firmado-*' -delete 2>/dev/null || true
    echo "PDFs de data/files limpiados."
  else
    echo "No existe $FILES_DIR (omitido)."
  fi
fi

echo "Listo. Usuarios, buzones, tipos y saldos anuales se mantienen."
