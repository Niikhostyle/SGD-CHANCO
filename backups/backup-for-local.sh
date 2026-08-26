#!/usr/bin/env bash
# Alias de backups/backup-prod.sh (compatibilidad).
# Uso: cd ~/sgd && bash backups/backup-for-local.sh
exec bash "$(dirname "$0")/backup-prod.sh" "$@"
