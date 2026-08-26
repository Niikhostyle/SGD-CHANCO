#!/usr/bin/env bash
# Backup semanal completo: local + Google Drive + retención.
# Uso en producción:
#   cd ~/sgd && bash backups/backup-weekly-gdrive.sh
#
# Cron sugerido (domingos 02:00):
#   0 2 * * 0 cd /home/sgdchanco/sgd && bash backups/backup-weekly-gdrive.sh >> /home/sgdchanco/backups_sgd/logs/cron.log 2>&1

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
SGD_ROOT="${SGD_ROOT:-$(cd "$SCRIPT_DIR/.." && pwd)}"
BACKUP_DIR="${BACKUP_DIR:-$HOME/backups_sgd}"
RCLONE_REMOTE="${RCLONE_REMOTE:-gdrive}"
GDRIVE_BASE="${GDRIVE_BASE:-SGD-CHANCO/backups}"
KEEP_LOCAL="${KEEP_LOCAL:-2}"    # copias locales a conservar
KEEP_REMOTE="${KEEP_REMOTE:-8}"  # semanas/carpetas en Drive a conservar
STAMP=$(date +%Y%m%d_%H%M)
LOG_DIR="$BACKUP_DIR/logs"
LOG_FILE="$LOG_DIR/weekly-${STAMP}.log"

mkdir -p "$LOG_DIR"

log() {
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "$LOG_FILE"
}

cleanup_local() {
  local pattern=$1
  local keep=$2
  mapfile -t files < <(ls -1t "$BACKUP_DIR"/$pattern 2>/dev/null || true)
  if ((${#files[@]} <= keep)); then
    return 0
  fi
  for ((i = keep; i < ${#files[@]}; i++)); do
    log "Eliminando local antiguo: ${files[$i]}"
    rm -f "${files[$i]}"
  done
}

cleanup_remote() {
  local keep=$1
  if ! command -v rclone >/dev/null 2>&1; then
    return 0
  fi
  mapfile -t folders < <(
    rclone lsf "${RCLONE_REMOTE}:${GDRIVE_BASE}/" --dirs-only 2>/dev/null \
      | sed 's:/$::' | sort -r || true
  )
  if ((${#folders[@]} <= keep)); then
    return 0
  fi
  for ((i = keep; i < ${#folders[@]}; i++)); do
    log "Eliminando en Drive carpeta antigua: ${folders[$i]}"
    rclone purge "${RCLONE_REMOTE}:${GDRIVE_BASE}/${folders[$i]}/" || \
      log "AVISO: no se pudo borrar ${folders[$i]}"
  done
}

log "========== Backup semanal SGD =========="
log "SGD_ROOT=$SGD_ROOT"

export SGD_ROOT BACKUP_DIR BACKUP_STAMP="$STAMP"
bash "$SCRIPT_DIR/backup-prod.sh" 2>&1 | tee -a "$LOG_FILE"

bash "$SCRIPT_DIR/backup-upload-gdrive.sh" "$STAMP" 2>&1 | tee -a "$LOG_FILE"

log "==> Retención local (últimos $KEEP_LOCAL)"
cleanup_local 'files_*.tar.gz' "$KEEP_LOCAL"
cleanup_local 'sgd_plc_*.dump' "$KEEP_LOCAL"
cleanup_local 'customize_*.tar.gz' "$KEEP_LOCAL"

log "==> Retención en Drive (últimas $KEEP_REMOTE carpetas)"
cleanup_remote "$KEEP_REMOTE"

log "========== Fin backup semanal =========="
