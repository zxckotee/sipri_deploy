#!/usr/bin/env bash
set -euo pipefail

# This script is executed by the official MariaDB image on first startup.
# It can access environment variables from docker-compose (.env interpolation).

mysql_exec() {
  mariadb --protocol=socket -uroot -p"${MYSQL_ROOT_PASSWORD}" -e "$1"
}

mysql_exec_db() {
  mariadb --protocol=socket -uroot -p"${MYSQL_ROOT_PASSWORD}" "$1" -e "$2"
}

SIPRI_DB_NAME="${SIPRI_DB_NAME:-sipri}"
SIPRI_DB_USER="${SIPRI_DB_USER:-sipri}"
SIPRI_DB_PASS="${SIPRI_DB_PASS:-sipri}"

APP_SIPRI_DB_NAME="${APP_SIPRI_DB_NAME:-app_sipri}"
APP_SIPRI_DB_USER="${APP_SIPRI_DB_USER:-app_sipri}"
APP_SIPRI_DB_PASS="${APP_SIPRI_DB_PASS:-app_sipri}"

# Создаём базы данных
mysql_exec "CREATE DATABASE IF NOT EXISTS \`${SIPRI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql_exec "CREATE DATABASE IF NOT EXISTS \`${APP_SIPRI_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Создаём пользователей и выдаём права
mysql_exec "CREATE USER IF NOT EXISTS '${SIPRI_DB_USER}'@'%' IDENTIFIED BY '${SIPRI_DB_PASS}';"
mysql_exec "GRANT ALL PRIVILEGES ON \`${SIPRI_DB_NAME}\`.* TO '${SIPRI_DB_USER}'@'%';"

mysql_exec "CREATE USER IF NOT EXISTS '${APP_SIPRI_DB_USER}'@'%' IDENTIFIED BY '${APP_SIPRI_DB_PASS}';"
mysql_exec "GRANT ALL PRIVILEGES ON \`${APP_SIPRI_DB_NAME}\`.* TO '${APP_SIPRI_DB_USER}'@'%';"

mysql_exec "FLUSH PRIVILEGES;"

# Проверяем, пуста ли база данных SIPRI (нет таблиц)
echo "Проверяю состояние базы данных ${SIPRI_DB_NAME}..."
TABLE_COUNT=$(mysql_exec_db "${SIPRI_DB_NAME}" "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${SIPRI_DB_NAME}';" 2>/dev/null | tail -n 1 | tr -d ' ' || echo "0")

if [ "$TABLE_COUNT" = "0" ] || [ -z "$TABLE_COUNT" ]; then
  echo "✓ База данных ${SIPRI_DB_NAME} пуста. Начинаю импорт dump.sql..."
  
  # Проверяем наличие файла dump.sql
  DUMP_FILE="/docker-entrypoint-initdb.d/dump.sql"
  if [ -f "$DUMP_FILE" ]; then
    echo "  → Найден файл dump.sql, начинаю импорт..."
    
    # Импортируем дамп, заменяя имя базы данных на лету
    if sed "s/\`cp91572_1\`/\`${SIPRI_DB_NAME}\`/g" "$DUMP_FILE" | \
       sed "s/USE \`cp91572_1\`/USE \`${SIPRI_DB_NAME}\`/g" | \
       sed "s/-- База данных: \`cp91572_1\`/-- База данных: \`${SIPRI_DB_NAME}\`/g" | \
       mariadb --protocol=socket -uroot -p"${MYSQL_ROOT_PASSWORD}" "${SIPRI_DB_NAME}" 2>&1; then
      
      # Проверяем, что импорт прошёл успешно
      NEW_TABLE_COUNT=$(mysql_exec_db "${SIPRI_DB_NAME}" "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${SIPRI_DB_NAME}';" 2>/dev/null | tail -n 1 | tr -d ' ' || echo "0")
      if [ "$NEW_TABLE_COUNT" != "0" ] && [ "$NEW_TABLE_COUNT" != "$TABLE_COUNT" ]; then
        echo "✓ Дамп успешно импортирован! Создано таблиц: ${NEW_TABLE_COUNT}"
      else
        echo "⚠ Импорт завершён, но количество таблиц не изменилось. Возможно, дамп уже был импортирован."
      fi
    else
      echo "✗ Ошибка при импорте дампа!"
      exit 1
    fi
  else
    echo "⚠ Файл dump.sql не найден в /docker-entrypoint-initdb.d/"
    echo "  База данных будет пустой. Импортируйте дамп вручную или добавьте файл dump.sql."
  fi
else
  echo "✓ База данных ${SIPRI_DB_NAME} уже содержит таблицы (${TABLE_COUNT} таблиц). Пропускаю импорт."
fi


