#!/bin/bash
# Скрипт для ручного импорта dump.sql

echo "Импортирую dump.sql в базу данных sipri_crm..."

# Получаем пароль и имя БД из контейнера
DB_ROOT_PASSWORD=$(sudo docker-compose exec -T db printenv MYSQL_ROOT_PASSWORD 2>/dev/null | tr -d '\r' || echo "44445555")
SIPRI_DB_NAME=$(sudo docker-compose exec -T db printenv SIPRI_DB_NAME 2>/dev/null | tr -d '\r' || echo "sipri_crm")

# Проверяем, какая база данных реально существует
EXISTING_DB=$(sudo docker-compose exec -T db mariadb --protocol=socket -uroot -p"${DB_ROOT_PASSWORD}" -e "SHOW DATABASES;" 2>/dev/null | grep -E "sipri|sipri_crm" | head -n 1 | tr -d ' ' || echo "")

if [ -n "$EXISTING_DB" ] && [ "$EXISTING_DB" != "$SIPRI_DB_NAME" ]; then
  echo "⚠ Обнаружена база данных: $EXISTING_DB (ожидалась: $SIPRI_DB_NAME)"
  echo "Использую существующую базу: $EXISTING_DB"
  SIPRI_DB_NAME="$EXISTING_DB"
fi

echo "Используемый пароль: ${DB_ROOT_PASSWORD:0:2}***"
echo "База данных: $SIPRI_DB_NAME"
echo ""

# Импортируем дамп, заменяя имя базы данных
sudo docker-compose exec -T db bash -c "
  sed 's/\`cp91572_1\`/\`${SIPRI_DB_NAME}\`/g' /docker-entrypoint-initdb.d/dump.sql | \
  sed 's/USE \`cp91572_1\`/USE \`${SIPRI_DB_NAME}\`/g' | \
  sed 's/-- База данных: \`cp91572_1\`/-- База данных: \`${SIPRI_DB_NAME}\`/g' | \
  mariadb --protocol=socket -uroot -p'${DB_ROOT_PASSWORD}' '${SIPRI_DB_NAME}'
"

if [ $? -eq 0 ]; then
  echo ""
  echo "✓ Импорт завершён успешно!"
  
  # Проверяем количество таблиц
  TABLE_COUNT=$(sudo docker-compose exec -T db mariadb --protocol=socket -uroot -p"${DB_ROOT_PASSWORD}" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${SIPRI_DB_NAME}';" 2>/dev/null | tail -n 1 | tr -d ' ')
  echo "Создано таблиц: $TABLE_COUNT"
else
  echo ""
  echo "✗ Ошибка при импорте!"
  exit 1
fi
