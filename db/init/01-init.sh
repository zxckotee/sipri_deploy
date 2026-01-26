#!/usr/bin/env bash
set -euo pipefail

# This script is executed by the official MariaDB image on first startup.
# It can access environment variables from docker-compose (.env interpolation).

mysql_exec() {
  mariadb --protocol=socket -uroot -p"${MYSQL_ROOT_PASSWORD}" -e "$1"
}

SIPRI_DB_NAME="${SIPRI_DB_NAME:-sipri_crm}"
SIPRI_DB_USER="${SIPRI_DB_USER:-sipri}"
SIPRI_DB_PASS="${SIPRI_DB_PASS:-sipri}"

APP_SIPRI_DB_NAME="${APP_SIPRI_DB_NAME:-app_sipri}"
APP_SIPRI_DB_USER="${APP_SIPRI_DB_USER:-app_sipri}"
APP_SIPRI_DB_PASS="${APP_SIPRI_DB_PASS:-app_sipri}"

mysql_exec "CREATE DATABASE IF NOT EXISTS \`${SIPRI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql_exec "CREATE DATABASE IF NOT EXISTS \`${APP_SIPRI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

mysql_exec "CREATE USER IF NOT EXISTS '${SIPRI_DB_USER}'@'%' IDENTIFIED BY '${SIPRI_DB_PASS}';"
mysql_exec "GRANT ALL PRIVILEGES ON \`${SIPRI_DB_NAME}\`.* TO '${SIPRI_DB_USER}'@'%';"

mysql_exec "CREATE USER IF NOT EXISTS '${APP_SIPRI_DB_USER}'@'%' IDENTIFIED BY '${APP_SIPRI_DB_PASS}';"
mysql_exec "GRANT ALL PRIVILEGES ON \`${APP_SIPRI_DB_NAME}\`.* TO '${APP_SIPRI_DB_USER}'@'%';"

mysql_exec "FLUSH PRIVILEGES;"


