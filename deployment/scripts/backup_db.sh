#!/bin/bash
# ==========================================
# Script de backup automático de base de datos
# ==========================================

set -e

# Configuración
DB_HOST=${DB_HOST:-mariadb}
DB_NAME=${DB_NAME:-becarios_ufps}
DB_USER=${DB_USER:-becarios_user}
DB_PASS=${DB_PASSWORD:-becarios_pass_2025!}
BACKUP_DIR="/app/backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="backup_becarios_${DATE}.sql"

# Crear directorio de backups
mkdir -p ${BACKUP_DIR}

echo "Iniciando backup de base de datos..."
echo "Fecha: $(date)"
echo "Base de datos: ${DB_NAME}"
echo "Servidor: ${DB_HOST}"

# Realizar backup
mysqldump -h${DB_HOST} -u${DB_USER} -p${DB_PASS} \
  --single-transaction \
  --routines \
  --triggers \
  --events \
  --add-drop-table \
  --add-locks \
  --create-options \
  --quick \
  --lock-tables=false \
  ${DB_NAME} > ${BACKUP_DIR}/${BACKUP_FILE}

# Comprimir backup
gzip ${BACKUP_DIR}/${BACKUP_FILE}

echo "✓ Backup completado: ${BACKUP_DIR}/${BACKUP_FILE}.gz"

# Limpiar backups antiguos (mantener últimos 7 días)
find ${BACKUP_DIR} -name "backup_becarios_*.sql.gz" -mtime +7 -delete

echo "✓ Backups antiguos limpiados"
echo "Backup finalizado exitosamente"