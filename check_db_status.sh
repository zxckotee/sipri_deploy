#!/bin/bash
# Скрипт для проверки статуса БД

echo "=== Проверка статуса базы данных ==="
echo ""

# Получаем пароль из переменных окружения контейнера
DB_ROOT_PASSWORD=$(sudo docker-compose exec -T db printenv MYSQL_ROOT_PASSWORD 2>/dev/null | tr -d '\r' || echo "root")

echo "Используемый пароль root: ${DB_ROOT_PASSWORD:0:1}***"
echo ""

# Проверяем подключение к БД
echo "1. Проверка подключения к MariaDB:"
if docker-compose exec -T db mariadb --protocol=socket -uroot -p"${DB_ROOT_PASSWORD}" -e "SELECT 1;" 2>&1 | grep -q "1"; then
  echo "✓ Подключение успешно"
else
  echo "✗ Ошибка подключения. Пробую альтернативный способ..."
  # Пробуем через TCP
  if docker-compose exec -T db mariadb -h 127.0.0.1 -uroot -p"${DB_ROOT_PASSWORD}" -e "SELECT 1;" 2>&1 | grep -q "1"; then
    echo "✓ Подключение через TCP успешно"
  else
    echo "✗ Не удалось подключиться. Проверьте пароль в .env или docker-compose.yml"
    exit 1
  fi
fi

echo ""
echo "2. Количество таблиц в sipri_crm:"
TABLE_COUNT=$(docker-compose exec -T db mariadb --protocol=socket -uroot -p"${DB_ROOT_PASSWORD}" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'sipri_crm';" 2>&1 | tail -n 1 | tr -d ' ' || echo "0")
if [ "$TABLE_COUNT" != "0" ] && [ -n "$TABLE_COUNT" ] && [[ "$TABLE_COUNT" =~ ^[0-9]+$ ]]; then
  echo "✓ Найдено таблиц: $TABLE_COUNT"
else
  echo "⚠ Таблиц не найдено или ошибка запроса (результат: $TABLE_COUNT)"
fi

echo ""
echo "3. Первые 10 таблиц:"
docker-compose exec -T db mariadb --protocol=socket -uroot -p"${DB_ROOT_PASSWORD}" sipri_crm -e "SHOW TABLES LIMIT 10;" 2>&1 | tail -n +2

echo ""
echo "4. Проверка таблиц с префиксом bro_ (RiseCRM):"
BRO_COUNT=$(docker-compose exec -T db mariadb --protocol=socket -uroot -p"${DB_ROOT_PASSWORD}" sipri_crm -e "SHOW TABLES LIKE 'bro_%';" 2>&1 | tail -n +2 | wc -l)
echo "Найдено таблиц bro_*: $BRO_COUNT"

echo ""
echo "5. Логи инициализации БД (последние 30 строк):"
docker-compose logs db 2>&1 | tail -n 30 | grep -E "импорт|import|table|дамп|База данных|Проверяю|init|Entrypoint|MariaDB" || echo "Логи не найдены"
