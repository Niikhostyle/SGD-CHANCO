#!/usr/bin/env bash
# Sube un backup local a Google Drive vía rclone.
# Uso:
#   bash backups/backup-upload-gdrive.sh                    # último backup
#   bash backups/backup-upload-gdrive.sh 20260826_1457      # stamp concreto
#
# Requiere: rclone remote "gdrive" configurado (rclone config).

set -euo pipefail

BACKUP_DIR="${BACKUP_DIR:-$HOME/backups_sgd}"
RCLONE_REMOTE="${RCLONE_REMOTE:-gdrive}"
GDRIVE_BASE="${GDRIVE_BASE:-SGD-CHANCO/backups}"
STAMP="${1:-$(cat "$BACKUP_DIR/.last_backup_stamp" 2>/dev/null || true)}"
LOG_DIR="$BACKUP_DIR/logs"

if [[ -z "$STAMP" ]]; then
  echo "ERROR: indica STAMP o ejecuta antes backups/backup-prod.sh"
  exit 1
fi

FILES="$BACKUP_DIR/files_${STAMP}.tar.gz"
DUMP="$BACKUP_DIR/sgd_plc_${STAMP}.dump"
CUSTOMIZE="$BACKUP_DIR/customize_${STAMP}.tar.gz"
FOLDER_DATE="${STAMP%%_*}"
DEST="${RCLONE_REMOTE}:${GDRIVE_BASE}/${FOLDER_DATE}"
LOG_FILE="$LOG_DIR/upload-${STAMP}.log"

mkdir -p "$LOG_DIR"

log() {
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "$LOG_FILE"
}

if ! command -v rclone >/dev/null 2>&1; then
  log "ERROR: rclone no instalado"
  exit 1
fi

if [[ ! -f "$FILES" ]]; then
  log "ERROR: no existe $FILES"
  exit 1
fi

if [[ ! -f "$DUMP" ]]; then
  log "ERROR: no existe $DUMP"
  exit 1
fi

log "==> Subiendo backup $STAMP a $DEST"

rclone mkdir "$DEST" 2>/dev/null || true

rclone copy "$BACKUP_DIR/" "$DEST/" \
  --include "files_${STAMP}.tar.gz" \
  --include "sgd_plc_${STAMP}.dump" \
  --include "customize_${STAMP}.tar.gz" \
  --drive-chunk-size "${RCLONE_CHUNK_SIZE:-128M}" \
  --transfers "${RCLONE_TRANSFERS:-2}" \
  --retries 5 \
  --low-level-retries 10 \
  --stats 1m \
  --log-file "$LOG_FILE" \
  --log-level INFO \
  -P

log "==> Verificación en Drive"
rclone ls "$DEST/" | tee -a "$LOG_FILE"
rclone size "$DEST/" | tee -a "$LOG_FILE"

log "==> Subida completada: $DEST"
