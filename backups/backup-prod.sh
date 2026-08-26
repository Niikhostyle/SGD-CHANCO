#!/usr/bin/env bash
# Backup local de producción: PostgreSQL + data/files (+ configs/customize).
# Uso en el servidor:
#   cd ~/sgd && bash backups/backup-prod.sh
#
# Variables opcionales:
#   SGD_ROOT, BACKUP_DIR, SGD_BD_CONTAINER, SGD_DB_USER, SGD_DB_NAME

set -euo pipefail

SGD_ROOT="${SGD_ROOT:-$HOME/sgd}"
BACKUP_DIR="${BACKUP_DIR:-$HOME/backups_sgd}"
CONTAINER="${SGD_BD_CONTAINER:-sgd_bd}"
DB_USER="${SGD_DB_USER:-sgd_plc}"
DB_NAME="${SGD_DB_NAME:-sgd_plc}"
STAMP="${BACKUP_STAMP:-$(date +%Y%m%d_%H%M)}"
LOG_DIR="$BACKUP_DIR/logs"

mkdir -p "$BACKUP_DIR" "$LOG_DIR"
LOG_FILE="$LOG_DIR/backup-${STAMP}.log"

log() {
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "$LOG_FILE"
}

docker_cmd() {
  if docker info >/dev/null 2>&1; then
    docker "$@"
  else
    sudo docker "$@"
  fi
}

if [[ ! -d "$SGD_ROOT/data/files" ]]; then
  log "ERROR: no existe $SGD_ROOT/data/files"
  exit 1
fi

cd "$SGD_ROOT"

log "==> Inicio backup $STAMP"
log "Tamaño data/files: $(du -sh data/files | cut -f1)"

log "==> Dump PostgreSQL ($CONTAINER / $DB_NAME)"
docker_cmd exec "$CONTAINER" pg_dump -U "$DB_USER" -d "$DB_NAME" -Fc -f /tmp/sgd_plc.dump
docker_cmd cp "$CONTAINER:/tmp/sgd_plc.dump" "$BACKUP_DIR/sgd_plc_${STAMP}.dump"
docker_cmd exec "$CONTAINER" rm -f /tmp/sgd_plc.dump
ln -sfn "$BACKUP_DIR/sgd_plc_${STAMP}.dump" "$BACKUP_DIR/sgd_plc.dump"

log "==> Empaquetando data/files (puede tardar varios minutos)"
# --warning=no-file-changed: el SGD sigue escribiendo PDFs durante el tar
tar --warning=no-file-changed -czf "$BACKUP_DIR/files_${STAMP}.tar.gz" -C data files || {
  rc=$?
  if [[ $rc -eq 1 ]]; then
    log "AVISO: tar terminó con archivos modificados durante la lectura (backup usable)"
  else
    log "ERROR: tar falló con código $rc"
    exit "$rc"
  fi
}
ln -sfn "$BACKUP_DIR/files_${STAMP}.tar.gz" "$BACKUP_DIR/files_backup.tar.gz"

if [[ -d "$SGD_ROOT/configs/customize" ]]; then
  log "==> Personalización (configs/customize)"
  tar -czf "$BACKUP_DIR/customize_${STAMP}.tar.gz" -C configs customize
fi

log "==> Backup local listo"
ls -lh "$BACKUP_DIR/sgd_plc_${STAMP}.dump" "$BACKUP_DIR/files_${STAMP}.tar.gz" | tee -a "$LOG_FILE"

# Exportar STAMP para scripts que encadenen subida
echo "$STAMP" > "$BACKUP_DIR/.last_backup_stamp"
