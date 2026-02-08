# СИПРИ (SIPRI) — сайт/CRM на CodeIgniter 4

Этот репозиторий содержит веб‑часть проекта СИПРИ: CodeIgniter 4 приложение (RiseCRM‑база) + модуль `SIPRI` (идеи/согласования/контакты/БЗ).

## Структура проекта
- `c:\sipri\public_html\crm\` — основное CI4 приложение (точка входа `index.php`)
  - `app/` — исходный код (контроллеры/модели/вьюхи/миграции)
  - `assets/` — фронтенд‑ассеты
  - `writable/` — кеш/логи/сессии/загрузки (должно быть доступно на запись)
- `c:\sipri\ТЗ СИПРИ версия 29.12.25.pptx` — актуальное ТЗ (PPTX)

## Требования окружения
- **PHP >= 7.4** (проверяется в `public_html\crm\index.php`)
- Web server: Nginx/Apache + PHP‑FPM
- База данных: MySQL/MariaDB (используется стандартная CI4 конфигурация)

## Запуск локально (общая схема)
1. Настроить виртуальный хост на `c:\sipri\public_html\crm\` (document root).
2. Создать БД и заполнить параметры подключения в CI4 конфиге/`.env`.
3. Открыть приложение в браузере и войти под админом.

## Миграции СИПРИ
СИПРИ расширяет схему БД через CI4 migrations в:
- `public_html\crm\app\Database\Migrations\`

Ключевые миграции:
- `2026-01-26-000001_CreateSipriCoreTables.php` — базовые таблицы (tenants/departments/ideas/votes/comments/approvals/attachments/history/delegations/locks)
- `2026-01-26-000002_AddSipriFieldsToUsers.php` — поля профиля СИПРИ в `users`
- `2026-01-26-000004_AddSipriAclToKnowledgeBase.php` — ACL поля в `help_categories`
- `2026-01-26-000005_AddSipriOrgRoleFields.php` — директор/руководители (org‑роли)
- `2026-01-26-000007_AddSipriExecutionFields.php` — исполнитель/отчёт/дата исполнения
- `2026-01-26-000008_AddSipriTenantCorpKey.php` — ключ корпорации (видимость смежных предприятий)
- `2026-01-26-000009_AddSipriExternalAccessSettings.php` — настройка `sipri_allow_clients`

Запуск миграций:
- В проекте есть админ‑маршрут установщика (см. `public_html\crm\app\Config\Routes.php`, контроллер `SipriInstaller`).

## Модуль СИПРИ: основные URL
- `sipri` — вход в модуль
- `sipri/ideas` — идеи
- `sipri/approvals` — согласования (только с правом `sipri_approve`)
- `sipri/contacts` — контакты
- `sipri/documents` — “Библиотека знаний” (SIPRI‑scope)
- `sipri/profile` — профиль СИПРИ (обязателен)
- `sipri/settings` — настройки СИПРИ (язык/веб‑уведомления)
- `sipri/support` — поддержка (перенаправляет в `tickets`, если модуль включён)
- `sipri/admin` — админка СИПРИ (справочники/настройки)
  - `sipri/tenants` — предприятия
  - `sipri/departments` — отделы/группы
  - `sipri/delegations` — делегирование полномочий

## Права доступа (RBAC)
Права добавлены в роли (см. `public_html\crm\app\Controllers\Roles.php` + view `roles/permissions.php`):
- `sipri_access` — доступ в СИПРИ (для staff)
- `sipri_manage` — управление справочниками/настройками СИПРИ
- `sipri_approve` — согласования
- `sipri_secret_access` — доступ к секретным карточкам

Клиенты (user_type=`client`):
- Доступ возможен только при включенной настройке `sipri_allow_clients=1` и если в профиле задан `sipri_tenant_id`.

## Организационная модель
Справочники:
- `sipri_tenants` — предприятия
  - `director_user_id` — директор (staff user)
  - `corp_key` — ключ “корпорации” для ограничения видимости смежных предприятий
- `sipri_departments` — отделы/группы
  - `manager_user_id` / `deputy_user_id` — руководитель/заместитель
  - `parent_id` — иерархия отделов

Делегирование:
- `sipri_delegations` — делегирование от `from_user_id` к `to_user_id` в рамках `tenant_id` + сроки действия.

## Идеи: жизненный цикл
Таблица `sipri_ideas`:
- `status`: `discussion` → `on_approval` → `approved` → `in_progress` → `done_success|done_fail` → `archived` (либо `rejected`)
- `rating_percent` — рейтинг по голосованию
- `executor_user_id` — назначенный исполнитель
- `execution_report`, `executed_at`, `executed_by` — отчёт и фиксация результата

Согласования:
- `sipri_idea_approvals` — записи согласующих
- Авто‑маршрут сейчас: **руководитель отдела → директор предприятия** (если задано). Делегат может принимать решения за делегирующего.

## Библиотека знаний (SIPRI)
Используется существующий модуль KB (таблицы `help_categories/help_articles`) в SIPRI‑режиме:
- категории типа `sipri_knowledge_base`
- ACL по `sipri_tenant_id` и (опционально) `sipri_department_id`

При создании нового предприятия в `sipri/tenants` автоматически создаются 4 базовых раздела:
- “Нормативные документы”
- “Документы предприятия”
- “Информационные ресурсы, новости”
- “Сайт предприятия”

## Интеграция (кнопка‑ссылка для сайтов предприятий)
Минимальный вариант интеграции из ТЗ (слайд 39):
- На сайте предприятия разместить кнопку/ссылку на `https://<ваш-домен>/crm/sipri`
- При необходимости — вынести ссылку в меню интранета/портала (например Bitrix)

Пример HTML:

```html
<a href=\"https://example.com/crm/sipri\" target=\"_blank\" rel=\"noopener\">СИПРИ</a>
```

## Документация по соответствию ТЗ
- Матрица требований: `public_html\crm\docs\sipri\spec-matrix-2026-01-26.md`

## Что ещё в воркспейсе
В воркспейсе также есть:
- `c:\app.sipri\` — отдельная админка приложения (PHP) — пока используется как standalone
- `c:\sipri_deploy\` — папка для деплой‑артефактов/скриптов (если используется вашей командой)


