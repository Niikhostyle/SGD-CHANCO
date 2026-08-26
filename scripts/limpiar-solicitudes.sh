#!/usr/bin/env bash
# Limpia SOLO solicitudes (y sus documentos SGD) y deja el próximo id en 1.
# Uso (en el servidor, desde la raíz del proyecto SGD):
#   bash scripts/limpiar-solicitudes.sh
#   bash scripts/limpiar-solicitudes.sh --con-archivos

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SQL="$ROOT/scripts/limpiar-solicitudes.sql"
CONTAINER="${SGD_BD_CONTAINER:-sgd_bd}"
DB_USER="${SGD_DB_USER:-sgd_plc}"
DB_NAME="${SGD_DB_NAME:-sgd_plc}"

if [[ ! -f "$SQL" ]]; then
  echo "No se encuentra $SQL"
  exit 1
fi

docker_cmd() {
  if docker info >/dev/null 2>&1; then
    docker "$@"
  else
    sudo docker "$@"
  fi
}

echo "==> ANTES (solicitudes + documentos SGD ligados)..."
docker_cmd exec -i "$CONTAINER" psql -U "$DB_USER" -d "$DB_NAME" -c \
  "SELECT count(*) AS solicitudes, coalesce(max(id),0) AS max_id FROM sol_solicitudes;
   SELECT count(*) AS docs_solicitudes FROM sol_solicitudes WHERE id_documento IS NOT NULL;
   SELECT 'documento' t, count(*) c FROM documento
   UNION ALL SELECT 'documento_buzon', count(*) FROM documento_buzon;"

echo "==> Ejecutando limpieza (solo solicitudes)..."
docker_cmd exec -i "$CONTAINER" psql -v ON_ERROR_STOP=1 -U "$DB_USER" -d "$DB_NAME" < "$SQL"

echo "==> DESPUÉS..."
docker_cmd exec -i "$CONTAINER" psql -U "$DB_USER" -d "$DB_NAME" -c \
  "SELECT count(*) AS solicitudes FROM sol_solicitudes;
   SELECT setval(pg_get_serial_sequence('sol_solicitudes','id'), 1, false) AS proximo_id_si_inserta;"

if [[ "${1:-}" == "--con-archivos" ]]; then
  echo "==> Limpiando PDFs de solicitudes en data/files..."
  FILES_DIR="$ROOT/data/files"
  if [[ -d "$FILES_DIR/solicitudes" ]]; then
    if find "$FILES_DIR/solicitudes" -type f \( -iname '*.pdf' -o -iname '*.PDF' \) -delete 2>/dev/null; then
      echo "PDFs en data/files/solicitudes eliminados."
    else
      echo "Reintentando con sudo..."
      sudo find "$FILES_DIR/solicitudes" -type f \( -iname '*.pdf' -o -iname '*.PDF' \) -delete
      echo "PDFs en data/files/solicitudes eliminados (sudo)."
    fi
  fi
  # PDFs principales de documentos SGD suelen estar en data/files/*.pdf;
  # no borramos todos los oficios: solo los que ya no tienen fila en documento.
  echo "Omitido borrado masivo de data/files (oficios intactos)."
fi

echo "Listo. Próxima solicitud debería salir con id = 1."
echo "Se mantienen: usuarios, buzones, plantillas, saldos y oficios del SGD."
