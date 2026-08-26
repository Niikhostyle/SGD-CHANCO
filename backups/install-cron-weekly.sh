#!/usr/bin/env bash
# Instala cron semanal para backup + Drive en el servidor de producción.
# Uso: bash backups/install-cron-weekly.sh

set -euo pipefail

SGD_ROOT="${SGD_ROOT:-$HOME/sgd}"
CRON_SCHEDULE="${CRON_SCHEDULE:-0 2 * * 0}"  # domingos 02:00
CRON_LINE="$CRON_SCHEDULE cd $SGD_ROOT && bash backups/backup-weekly-gdrive.sh >> $HOME/backups_sgd/logs/cron.log 2>&1"
MARKER="# sgd-weekly-backup-gdrive"

mkdir -p "$HOME/backups_sgd/logs"

chmod +x "$SGD_ROOT/backups/backup-prod.sh" \
         "$SGD_ROOT/backups/backup-upload-gdrive.sh" \
         "$SGD_ROOT/backups/backup-weekly-gdrive.sh"

if crontab -l 2>/dev/null | grep -qF "$MARKER"; then
  echo "Cron ya instalado:"
  crontab -l | grep -F "$MARKER" -A1
  exit 0
fi

(crontab -l 2>/dev/null || true; echo "$MARKER"; echo "$CRON_LINE") | crontab -

echo "Cron instalado:"
crontab -l | grep -F "$MARKER" -A1
echo ""
echo "Programación: $CRON_SCHEDULE (domingos 02:00 por defecto)"
echo "Logs: $HOME/backups_sgd/logs/cron.log"
