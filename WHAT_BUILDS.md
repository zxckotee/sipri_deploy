# Что попадает в Docker сборку и как монтируется код

Ваше целевое окружение:
- `sipri_deploy/` находится рядом с `sipri/` и `app.sipri/`

## 1) Контекст сборки (build context)
В `docker-compose.yml` для `sipri` и `app_sipri` указано:
- `build.context: .` → **контекстом сборки является папка `sipri_deploy/`**

Это означает:
- в образ **НЕ копируется** исходный код из `../sipri` или `../app.sipri`;
- в образ попадают только файлы из `sipri_deploy/`, которые Dockerfile явно `COPY`-ит.

## 2) Что реально копируется в образ

### sipri image
- Dockerfile: `sipri_deploy/docker/sipri/Dockerfile`
- COPY:
  - `docker/sipri/apache.conf` → `/etc/apache2/sites-available/000-default.conf`
- Всё остальное (код CI4) приходит **через volume**.

### app_sipri image
- Dockerfile: `sipri_deploy/docker/app_sipri/Dockerfile`
- COPY:
  - `docker/app_sipri/apache.conf` → `/etc/apache2/sites-available/000-default.conf`
- Всё остальное (код app.sipri) приходит **через volume**.

## 3) Как код попадает в контейнер (volume mounts)
В `docker-compose.yml`:
- SIPRI (порт 80):
  - `../sipri/public_html/crm:/var/www/html`
- app.sipri (порт 3000):
  - `../app.sipri/public_html:/var/www/html`

То есть контейнеры запускаются с **bind-mount** исходников из соседних папок.

## 4) Если нужны “immutable images” (код внутри образа)
Текущая схема ориентирована на dev/стенд и быстрые правки.

Для “код внутри образа” нужно менять подход:
- либо менять `build.context` так, чтобы он включал `sipri/` и `app.sipri/` (и добавить `.dockerignore` на уровне контекста),
- либо собирать образы отдельно и публиковать в registry,
- либо копировать артефакты сборки в `sipri_deploy/` и делать `COPY` их в образ.

## 5) Как используется .env
Docker Compose автоматически читает файл `.env` из каталога `sipri_deploy/`.
Он используется для:
- интерполяции `${VAR}` в `docker-compose.yml`
- передачи переменных в контейнеры (в т.ч. в MariaDB init script `db/init/01-init.sh`)


