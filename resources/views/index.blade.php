<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $data['title'] ?? 'Swagger UI' }}</title>

    <link rel="stylesheet" href="{{ MODX_BASE_URL }}assets/modules/swagger-ui/swagger-ui.css">
    <style>
        html { box-sizing: border-box; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
        
        /* Кастомные стили */
        {{ $config['custom_css'] ?? '' }}
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="{{ MODX_BASE_URL }}assets/modules/swagger-ui/swagger-ui-bundle.js"></script>
    <script src="{{ MODX_BASE_URL }}assets/modules/swagger-ui/swagger-ui-standalone-preset.js"></script>

    <script>
        window.onload = function () {
            // Получаем конфигурации из PHP через JSON
            const config = @json($config ?? []);
            const data = @json($data ?? []);
            
            // Извлекаем подсекции
            const display = config.display || {};
            const network = config.network || {};
            const sorting = config.sorting || {};
            const requestSnippets = config.request_snippets || {};
            const macros = config.macros || {};
            const events = config.events || {};
            const authorization = config.authorization || {};
            
            // Преобразуем фильтр в правильный формат
            let filterValue = display.filter;
            if (filterValue === true || filterValue === false) {
                filterValue = filterValue;
            } else if (typeof filterValue === 'string') {
                filterValue = filterValue;
            } else {
                filterValue = false;
            }
            
            // Создаем конфигурацию для Swagger UI
            const swaggerConfig = {
                @if(isset($data['urls']))
                urls: @json($data['urls']),
                "urls.primaryName": "{{ $data['default'] ?? 'v1' }}",
                @endif
                dom_id: '#swagger-ui',
                
                // Настройки отображения
                deepLinking: {{ $config['display']['deep_linking'] ?? 'true' ? 'true' : 'false' }},
                displayOperationId: {{ $config['display']['display_operation_id'] ?? 'false' ? 'true' : 'false' }},
                defaultModelsExpandDepth: {{ $config['display']['default_models_expand_depth'] ?? 1 }},
                defaultModelExpandDepth: {{ $config['display']['default_model_expand_depth'] ?? 1 }},
                defaultModelRendering: "{{ $config['display']['default_model_rendering'] ?? 'example' }}",
                displayRequestDuration: {{ $config['display']['display_request_duration'] ?? 'false' ? 'true' : 'false' }},
                docExpansion: "{{ $config['display']['doc_expansion'] ?? 'list' }}",
                @if(isset($config['display']['filter']))
                    @if(is_bool($config['display']['filter']))
                filter: {{ $config['display']['filter'] ? 'true' : 'false' }},
                    @else
                filter: {{ $config['display']['filter'] }},
                    @endif
                @else
                filter: false,
                @endif
                maxDisplayedTags: {{ $config['display']['max_displayed_tags'] ?? 'null' }},
                showExtensions: {{ $config['display']['show_extensions'] ?? 'false' ? 'true' : 'false' }},
                showCommonExtensions: {{ $config['display']['show_common_extensions'] ?? 'false' ? 'true' : 'false' }},
                tryItOutEnabled: {{ $config['display']['try_it_out_enabled'] ?? 'false' ? 'true' : 'false' }},
                requestSnippetsEnabled: {{ $config['display']['request_snippets_enabled'] ?? 'false' ? 'true' : 'false' }},
                persistAuthorization: {{ ($config['authorization']['persist_authorization'] ?? true) ? 'true' : 'false' }},
                // Синтаксис подсветки
                syntaxHighlight: {
                    activated: {{ $config['display']['syntax_highlight']['activated'] ?? 'true' ? 'true' : 'false' }},
                    theme: "{{ $config['display']['syntax_highlight']['theme'] ?? 'agate' }}"
                },
                // Настройки сортировки
                @if(isset($config['sorting']['operations_sorter']) && $config['sorting']['operations_sorter'])
                operationsSorter: "{{ $config['sorting']['operations_sorter'] }}",
                @endif
                @if(isset($config['sorting']['tags_sorter']) && $config['sorting']['tags_sorter'])
                tagsSorter: "{{ $config['sorting']['tags_sorter'] }}",
                @endif
                
                // Настройки сети
                @if(isset($config['network']['validator_url']))
                validatorUrl: {{ $config['network']['validator_url'] }},
                @else
                validatorUrl: null,
                @endif
                withCredentials: {{ $config['network']['with_credentials'] ?? 'false' ? 'true' : 'false' }},
                supportedSubmitMethods: @json($config['network']['supported_submit_methods'] ),
                
                // Сниппеты запросов
                @if(isset($config['request_snippets']['generators']))
                requestSnippets: {
                    generators: @json($config['request_snippets']['generators']),
                    defaultExpanded: {{ $config['request_snippets']['defaultExpanded'] ?? 'true' ? 'true' : 'false' }},
                    @if(isset($config['request_snippets']['languages']))
                    languages: @json($config['request_snippets']['languages']),
                    @endif
                },
                @endif
                
                @if(isset($securityConfig) && isset($securityConfig['securitySchemes']))
                components: {
                    securitySchemes: @json($securityConfig['securitySchemes'])
                },
                @endif

                // Пресеты и layout
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "{{ $config['layout'] ?? 'StandaloneLayout' }}",
            };
            
            // Добавляем плагины, если есть
            @if(isset($config['plugins']) && !empty($config['plugins']))
            swaggerConfig.plugins = [
                @foreach($config['plugins'] as $plugin)
                {!! $plugin !!},
                @endforeach
            ];
            @endif
            
            @if(isset($securityConfig['security']) && !empty($securityConfig['security']))
            swaggerConfig.security = @json($securityConfig['security']);
            @endif

            // Создаем экземпляр Swagger UI
            const ui = SwaggerUIBundle(swaggerConfig);
            window.ui = ui;
            
            // Макросы
            @if(isset($config['macros']['model_property_macro']) && $config['macros']['model_property_macro'])
            ui.modelPropertyMacro = function(property) {
                {!! $config['macros']['model_property_macro'] !!}
            };
            @endif
            
            @if(isset($config['macros']['parameter_macro']) && $config['macros']['parameter_macro'])
            ui.parameterMacro = function(operation, parameter) {
                {!! $config['macros']['parameter_macro'] !!}
            };
            @endif
            
            // События
            @if(isset($config['events']['on_complete']) && $config['events']['on_complete'])
            ui.onComplete = function() {
                {!! $config['events']['on_complete'] !!}
            };
            @endif
        };
    </script>
</body>
</html>