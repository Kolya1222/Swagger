<?php

namespace roilafx\swaggeruievo\Controllers;

use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SwaggerViewController
{
    public function __invoke() : View
    {
        $config = config('swagger-ui', []);
        $uiConfig = $config['ui'] ?? [];
        
        // Проверяем, включен ли Swagger UI
        if (!($uiConfig['enabled'] ?? true)) {
            throw new HttpException(404, 'Swagger UI is disabled');
        }
        
        // Подготавливаем URLs для Swagger UI
        $urls = [];
        $default = $uiConfig['default'] ?? 'v1';
        
        foreach ($uiConfig['urls'] ?? ['v1' => 'openapi.json'] as $name => $path) {
            $fullUrl = asset('/modules/swagger-ui/' . ltrim($path, '/'));
            $urls[] = [
                'url' => $fullUrl,
                'name' => $name,
            ];
        }
        
        // Подготавливаем ВСЕ конфигурации для передачи в шаблон
        $swaggerConfig = [
            'display' => $uiConfig['display'] ?? [],
            'network' => $uiConfig['network'] ?? [],
            'sorting' => $uiConfig['sorting'] ?? [],
            'request_snippets' => $uiConfig['request_snippets'] ?? [],
            'macros' => $uiConfig['macros'] ?? [],
            'events' => $uiConfig['events'] ?? [],
            'plugins' => $uiConfig['plugins'] ?? [],
            'layout' => $uiConfig['layout'] ?? 'StandaloneLayout',
            'custom_css' => $uiConfig['custom_css'] ?? '',
        ];
        
        return view('swagger-ui::index', [
            'data' => [
                'title' => $uiConfig['title'] ?? 'API Documentation',
                'urls' => $urls,
                'default' => $default,
            ],
            'config' => $swaggerConfig,
            'uiConfig' => $uiConfig, // Передаем все конфиги
        ]);
    }   
}