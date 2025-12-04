<?php

return [
    // Основные настройки
    'ui' => [
        'enabled' => env('SWAGGER_UI_ENABLED', true),
        'title' => env('SWAGGER_UI_TITLE', env('APP_NAME', 'EvolutionCMS') . ' - API Documentation'),
        
        // Пути к API спецификациям
        'urls' => [
            'v1' => 'openapi.json',
        ],
        'default' => 'v1',
        
        // Настройки отображения
        'display' => [
            'deep_linking' => true,
            'display_operation_id' => false,
            'default_models_expand_depth' => 1,
            'default_model_expand_depth' => 1,
            'default_model_rendering' => 'example', // 'example' или 'model'
            'display_request_duration' => false,
            'doc_expansion' => 'none', // 'list', 'full', 'none'
            'filter' => false, // true/false или строка для фильтра
            'max_displayed_tags' => null,
            'show_extensions' => false,
            'show_common_extensions' => false,
            'try_it_out_enabled' => false,
            'request_snippets_enabled' => false,
            'syntax_highlight' => [
                'activated' => true,
                'theme' => 'agate', // 'agate', 'arta', 'monokai', 'nord', 'obsidian', 'tomorrow-night', 'idea'
            ],
        ],
        
        // Сортировка
        'sorting' => [
            'operations_sorter' => null, // 'alpha', 'method' или функция
            'tags_sorter' => null, // 'alpha' или функция
        ],
        
        // Стилизация
        'custom_css' => '', // Кастомные CSS стили
        
        // Сниппеты запросов
        'request_snippets' => [
            'generators' => [
                'curl_bash' => [
                    'title' => 'cURL (bash)',
                    'syntax' => 'bash',
                ],
                'curl_powershell' => [
                    'title' => 'cURL (PowerShell)',
                    'syntax' => 'powershell',
                ],
                'curl_cmd' => [
                    'title' => 'cURL (CMD)',
                    'syntax' => 'bash',
                ],
            ],
            'defaultExpanded' => true,
            'languages' => null, // Например: ['curl_bash']
        ],
        
        // Сеть и безопасность
        'network' => [
            'validator_url' => null, // null для отключения или URL валидатора
            'with_credentials' => false,
            'supported_submit_methods' => ['get', 'put', 'post', 'delete', 'options', 'head', 'patch', 'trace'],
        ],
        
        // Макросы (JavaScript функции)
        'macros' => [
            'model_property_macro' => null, // JavaScript функция
            'parameter_macro' => null, // JavaScript функция
        ],
        
        // Обработчики событий
        'events' => [
            'on_complete' => null, // JavaScript функция
        ],
        
        // Плагины
        'plugins' => [], // Массив JavaScript функций плагинов
        
        // Layout
        'layout' => 'StandaloneLayout', // Или кастомный layout
    ],
    
    // Настройки генератора документации
    'generator' => [
        'enabled' => env('SWAGGER_GENERATOR_ENABLED', true),
        
        'output_path' => env('SWAGGER_OUTPUT_PATH', 
            MODX_BASE_PATH . 'assets/modules/swagger-ui/openapi.json'
        ),
        
        'scan_paths' => [
            base_path('vendor/roilafx/evolutionapi/'),
        ],
        
        'exclude' => [
            base_path('vendor/roilafx/evolutionapi/routes'),
        ],
        
        'pattern' => '*.php',
        
        'server_url' => env('MODX_SITE_URL', 'http://localhost'),
    ],
];