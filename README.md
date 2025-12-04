# Swagger UI для Evolution CMS

Пакет для автоматической генерации OpenAPI документации и отображения Swagger UI в админ-панели Evolution CMS. Поддерживает интеграцию с EvolutionAPI и другими модулями.

## Возможности

- Автоматическая генерация OpenAPI документации из PHP аннотаций
- Встроенный Swagger UI интерфейс в админ-панели Evolution CMS
- Гибкая настройка через конфигурационный файл и переменные окружения
- Поддержка нескольких версий API
- Консольная команда для генерации документации
- Кастомизация внешнего вида и поведения Swagger UI
- Поддержка сканирования EvolutionAPI и других пакетов

## Установка

### 1. Установка пакета

```bash
php artisan package:installrequire roilafx/swaggeruievo "*"
```

2. Публикация стилей и скриптов
```
php artisan vendor:publish --provider="roilafx\swaggeruievo\swaggeruievoServiceProvider"

### 3. Настройка переменных окружения (опционально важный MODX_SITE_URL)

Добавьте в файл `.env` вашего проекта:

```env
# Swagger UI настройки
SWAGGER_UI_ENABLED=true
SWAGGER_UI_TITLE="Мой проект - API Documentation"
SWAGGER_GENERATOR_ENABLED=true
SWAGGER_OUTPUT_PATH="/путь/к/openapi.json"
MODX_SITE_URL="https://ваш-сайт.com"
```

## Конфигурация

Пакет использует файл конфигурации с широкими возможностями настройки:

### Основные настройки UI

```php
'ui' => [
    'enabled' => true, // Включить/выключить Swagger UI
    'title' => 'API Documentation', // Заголовок страницы
    'urls' => [
        'v1' => 'openapi.json', // Пути к спецификациям API
    ],
],
```

### Настройки отображения

```php
'display' => [
    'doc_expansion' => 'none', // Сворачивание/разворачивание документации
    'filter' => false, // Поиск/фильтрация
    'syntax_highlight' => [
        'theme' => 'agate', // Тема подсветки синтаксиса
    ],
],
```

### Настройки генератора документации

```php
'generator' => [
    'enabled' => true, // Включить генерацию
    'output_path' => 'assets/modules/swagger-ui/openapi.json', // Путь для сохранения
    'scan_paths' => [
        base_path('vendor/roilafx/evolutionapi/'), // Сканировать EvolutionAPI
        base_path('/assets/modules/'), // Сканировать модули
    ],
    'server_url' => 'http://ваш-сайт.com', // Базовый URL сервера
],
```

## Использование

### Генерация документации

```bash
# Базовая генерация документации
php artisan swagger:generate

# С очисткой кэша
php artisan swagger:generate --clear

# Принудительная перезапись существующего файла
php artisan swagger:generate --force

# Комбинированные опции
php artisan swagger:generate --clear --force
```

### Доступ к Swagger UI

После успешной генерации документации:
1. Войдите в админ-панель Evolution CMS
2. Перейдите в раздел "Модули"
3. Найдите и откройте "Swagger UI Documentation"

### Интеграция с EvolutionAPI

Пакет автоматически сканирует и документирует EvolutionAPI если он установлен.

## Аннотации OpenAPI

### Пример контроллера с аннотациями

```php
<?php

namespace roilafx\Evolutionapi\Docs;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    openapi: '3.0.0',
    info: new OA\Info(
        version: '1.0.0',
        title: 'Evolution CMS API',
        description: 'REST API для управления Evolution CMS',
        contact: new OA\Contact(
            name: 'API Support',
            email: 'belov.belov-ik@yandex.ru'
        )
    ),
    externalDocs: new OA\ExternalDocumentation(
        description: 'Документация Evolution CMS',
        url: 'https://docs.evo-cms.com'
    )
)]
class OpenApi
{
}
```

## Кастомизация

### Кастомные CSS стили

```php
'custom_css' => '
    .swagger-ui .info {
        margin: 20px 0;
    }
    .swagger-ui .opblock-tag {
        font-size: 24px;
        margin: 0 0 15px;
    }
',
```

### Плагины и макросы

```php
'plugins' => [
    'function MyPlugin() {
        return {
            statePlugins: {
                spec: {
                    wrapSelectors: {
                        allowTryItOutFor: () => () => true
                    }
                }
            }
        }
    }',
],

'macros' => [
    'model_property_macro' => 'function(prop, required) {
        return prop.description || prop.example ? 
            prop.description + " (пример: " + prop.example + ")" : 
            prop.description;
    }',
],
```

## Переменные окружения

| Переменная | Описание | По умолчанию |
|------------|----------|--------------|
| `SWAGGER_UI_ENABLED` | Включить Swagger UI | `true` |
| `SWAGGER_UI_TITLE` | Заголовок документации | `EvolutionCMS - API Documentation` |
| `SWAGGER_GENERATOR_ENABLED` | Включить генератор | `true` |
| `SWAGGER_OUTPUT_PATH` | Путь для сохранения openapi.json | `assets/modules/swagger-ui/openapi.json` |
| `MODX_SITE_URL` | Базовый URL API | `http://localhost` |

## Команды Artisan

| Команда | Описание |
|---------|----------|
| `php artisan swagger:generate` | Генерировать документацию |
| `php artisan swagger:generate --clear` | Генерировать с очисткой кэша |
| `php artisan swagger:generate --force` | Принудительная перезапись |

## Расширенные возможности

### Поддержка нескольких API версий

```php
'urls' => [
    'v1' => 'openapi-v1.json',
    'v2' => 'openapi-v2.json',
    'beta' => 'openapi-beta.json',
],
'default' => 'v2',
```

### Кастомизация темы подсветки синтаксиса

```php
'syntax_highlight' => [
    'activated' => true,
    'theme' => 'agate', // Доступные: agate, arta, monokai, nord, obsidian, tomorrow-night, idea
],
```

### Валидация API спецификаций

```php
'network' => [
    'validator_url' => 'https://validator.swagger.io/validator/debug',
    // или отключить валидацию:
    // 'validator_url' => null,
],
```

## Устранение неполадок

### Документация не генерируется
1. Проверьте права на запись в директорию назначения
2. Убедитесь, что `SWAGGER_GENERATOR_ENABLED=true`
3. Проверьте пути сканирования в конфигурации
4. Используйте `--clear` для очистки кэша


## Безопасность

### Рекомендации для продакшена

1. **Отключите Try It Out в продакшене:**
```php
'try_it_out_enabled' => false,
```

2. **Ограничьте доступ к Swagger UI:**
```php
'enabled' => env('APP_DEBUG', false),
```

3. **Используйте аутентификацию:**
```php
'network' => [
    'with_credentials' => true,
],
```

4. **Не включайте в продакшене:**
```php
'display' => [
    'filter' => false, // Поиск может раскрыть внутреннюю структуру
],
```

## Примеры интеграции

### С EvolutionAPI

```php
'generator' => [
    'scan_paths' => [
        base_path('vendor/roilafx/evolutionapi/'),
    ],
    'exclude' => [
        base_path('vendor/roilafx/evolutionapi/routes'),
        base_path('storage/'),
        base_path('vendor/*/tests/'),
    ],
],
```

### С кастомными модулями

```php
'generator' => [
    'scan_paths' => [
        base_path('vendor/roilafx/evolutionapi/'),
        base_path('assets/modules/my-module/Controllers/'),
        base_path('assets/modules/another-module/Api/'),
    ],
],
```
