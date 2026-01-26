# Деплой SIPRI + app.sipri через Docker (Apache + MariaDB)

Цели окружения:
- **SIPRI** доступен на **80 порту**
- **app.sipri** доступен на **3000 порту**
- **MariaDB** поднимается как отдельный сервис

## 1) Подготовка

### 1.1 Требования
- Docker + Docker Compose
- Доступ к исходникам:
  - `c:\sipri\public_html\crm`
  - `c:\app.sipri\public_html`
  - `c:\sipri_deploy` (этот каталог)

### 1.2 Переменные окружения compose
Файл `env.sample` содержит пример значений.  
Создайте рядом файл `.env` (Docker Compose читает `.env`) и заполните:

- `DB_ROOT_PASSWORD`
- `SIPRI_DB_NAME`, `SIPRI_DB_USER`, `SIPRI_DB_PASS`
- `APP_SIPRI_DB_NAME`, `APP_SIPRI_DB_USER`, `APP_SIPRI_DB_PASS`

Важно: имя файла `.env` может быть запрещено политиками репозитория, но для **деплоя** он всё равно нужен. Если нельзя хранить `.env` в репо — держите его только на сервере.

## 2) Запуск

Из каталога `c:\sipri_deploy`:

```bash
docker compose up -d --build
```

Проверка:
- SIPRI: `http://localhost/`
- app.sipri: `http://localhost:3000/`
- MariaDB: порт `3306`

## 3) База данных

### 3.1 Инициализация
MariaDB при первом запуске выполнит `db/init/01-init.sql` и создаст:
- БД `sipri_crm`, пользователь `sipri`
- БД `app_sipri`, пользователь `app_sipri`

### 3.2 Импорт схемы app.sipri (если нужно)
Файл `c:\app.sipri\public_html\sql.txt` содержит дамп. Для импорта:

```bash
docker exec -i sipri_db mariadb -uroot -p"$DB_ROOT_PASSWORD" app_sipri < ../app.sipri/public_html/sql.txt
```

## 4) SIPRI (CI4/RiseCRM): настройка БД и миграции

### 4.1 Подключение к БД
В вашем CI4 проекте подключение к БД задаётся через конфиг/`.env` внутри `c:\sipri\public_html\crm`.
В docker-compose мы пробрасываем переменные `SIPRI_DB_*`, но **сам проект должен их использовать** (через `.env` или `app/Config/Database.php`).

Рекомендуемый вариант:
- настроить `.env` в `c:\sipri\public_html\crm` так, чтобы он указывал на хост `db` и базу `sipri_crm`.

### 4.2 Миграции СИПРИ
После того как CI4 подключился к БД, выполните миграции:

Вариант A (через админ‑маршрут установщика, если включён):
- открыть маршрут установщика миграций (см. `crm/app/Config/Routes.php` и `SipriInstaller`)

Вариант B (CLI внутри контейнера):

```bash
docker exec -it sipri_web php spark migrate
```

## 5) app.sipri: подключение к БД через env

Мы вынесли настройки БД в env:
- `APP_SIPRI_DB_HOST=db`
- `APP_SIPRI_DB_PORT=3306`
- `APP_SIPRI_DB_NAME`, `APP_SIPRI_DB_USER`, `APP_SIPRI_DB_PASS`

Файлы:
- `c:\app.sipri\public_html\config\DBSettings.php`
- `c:\app.sipri\public_html\config\DBClass.php`

## 6) Полезные команды

Логи:

```bash
docker logs -f sipri_web
docker logs -f app_sipri_web
docker logs -f sipri_db
```

Перезапуск:

```bash
docker compose restart
```

Остановка:

```bash
docker compose down
```

Полная очистка БД (удалит данные):

```bash
docker compose down -v
```


