# План работ: деплой SIPRI + app.sipri (Apache + SQL) и доработки под окружение

Цели:
- Развернуть **SIPRI** (CI4/RiseCRM) на **80 порту**.
- Развернуть **app.sipri** (PHP admin panel, без Flutter) на **3000 порту**.
- Поднять **SQL** (MariaDB) и обеспечить воспроизводимый деплой через Docker.
- Учесть необходимые доработки `app.sipri` для корректной работы в контейнере.

---

## 1) Доработки app.sipri (для Docker/production)

### 1.1 Конфигурация БД через env (обязательно)
Проблема: сейчас `c:\app.sipri\public_html\config\DBSettings.php` содержит хардкод логина/пароля/хоста.

Решение:
- Читать параметры из переменных окружения:
  - `APP_SIPRI_DB_HOST`
  - `APP_SIPRI_DB_NAME`
  - `APP_SIPRI_DB_USER`
  - `APP_SIPRI_DB_PASS`
  - `APP_SIPRI_DB_PORT` (опционально)
- Оставить безопасные дефолты (например `db`, `app_sipri`, `app_sipri`, `app_sipri`), чтобы работало в `docker-compose`.

### 1.2 Кодировка соединения (рекомендуется)
- Перевести на `utf8mb4` (если поддерживается схемой) либо оставить `utf8`, но зафиксировать в одном месте.

### 1.3 Сессии/безопасность (минимум)
- В Docker окружении обеспечить `session.save_path` на writable volume.
- Убедиться, что `session.cookie_secure` корректно выставляется при HTTPS (опционально).

---

## 2) Docker деплой (sipri_deploy)

В `c:\sipri_deploy\` будут созданы:
- `docker-compose.yml` (3 сервиса: `sipri`, `app_sipri`, `db`)
- `docker/sipri/Dockerfile` + Apache conf
- `docker/app_sipri/Dockerfile` + Apache conf
- `db/init/01-init.sql` (создание БД/пользователей для обоих приложений)
- `DEPLOY.md` (пошаговая инструкция: build/run/migrate)

### 2.1 Порты
- `sipri`: `80:80`
- `app_sipri`: `3000:80`
- `db`: `3306:3306` (можно закрыть наружу при необходимости)

### 2.2 Зависимости PHP/Apache
Для обоих контейнеров:
- `php:<8.x>-apache`
- модули Apache: `rewrite`, `headers`
- расширения PHP: `mysqli`, `pdo_mysql`, `intl`, `gd`, `zip`, `mbstring`, `curl`, `xml`, `fileinfo`

### 2.3 Точки монтирования кода
- `sipri` контейнер монтирует: `c:\sipri\public_html\crm` → `/var/www/html`
- `app_sipri` контейнер монтирует: `c:\app.sipri\public_html` → `/var/www/html`

### 2.4 Инициализация БД
`db/init/01-init.sql`:
- создаёт базы: `sipri_crm`, `app_sipri`
- создаёт пользователей и выдаёт права

После старта:
- SIPRI: запустить CI4 миграции (через админ‑маршрут установщика/или CLI внутри контейнера).
- app.sipri: импортировать `sql.txt` (опционально, если нужна начальная схема/данные).

---

## 3) Проверка работоспособности (smoke checklist)
- `http://localhost/` (SIPRI): открывается экран входа CI4, после логина доступен `sipri/*`.
- `http://localhost:3000/` (app.sipri): открывается login.
- БД соединение работает из обоих контейнеров.
- Writable директории (логи/кеш/загрузки) не падают по правам.

---

## 4) Риски/оговорки
- SIPRI/RiseCRM может ожидать определённую схему БД и seed‑данные. Миграции СИПРИ добавляют свои таблицы поверх существующей схемы.
- app.sipri `sql.txt` большой и содержит демо‑данные; в production лучше мигрировать только структуру и нужные записи.


