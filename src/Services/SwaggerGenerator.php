<?php

namespace roilafx\swaggeruievo\Services;

use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SwaggerGenerator
{
    protected array $config;
    
    public function __construct()
    {
        $this->config = config('swagger-ui.generator', []);
    }
    
    /**
     * Генерирует OpenAPI документацию из аннотаций
     *
     * @return bool
     * @throws \Exception
     */
    public function generate(): bool
    {
        if (!($this->config['enabled'] ?? true)) {
            throw new \Exception('Swagger generator is disabled in configuration.');
        }
        
        $this->prepareDirectory();
        
        $this->info('Starting OpenAPI documentation generation...');
        
        try {
            // Сканирование аннотаций
            $openapi = $this->scanAnnotations();
            
            if (!$openapi) {
                throw new \Exception('Failed to scan annotations. No OpenAPI object generated.');
            }
            
            // Конвертируем в массив для модификации
            $spec = json_decode($openapi->toJson(), true);
            
            // Добавляем серверы
            $spec = $this->addServers($spec);
            
            // Добавляем тэги
            $spec = $this->addTags($spec);
            
            // Сохраняем файл
            return $this->saveToFile($spec);
            
        } catch (\Exception $e) {
            $this->error('Generation error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Сканирует аннотации в указанных директориях
     *
     * @return \OpenApi\Annotations\OpenApi|null
     */
    protected function scanAnnotations(): ?OA\OpenApi
    {
        $scanPaths = $this->config['scan_paths'] ?? [base_path('app')];
        $exclude = $this->config['exclude'] ?? [];
        try {
            $openapi = \OpenApi\Generator::scan($scanPaths, [
                'exclude' => $exclude,
                'validate' => false
            ]);
            return $openapi;
            
        } catch (\Exception $e) {
            $this->error('Scan error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return null;
        }
    }
    
    /**
     * Добавляет серверы в спецификацию
     */
    protected function addServers(array $spec): array
    {
        $serverUrl = $this->config['server_url'] ?? null;
        
        if ($serverUrl && empty($spec['servers'])) {
            $spec['servers'] = [
                [
                    'url' => rtrim($serverUrl, '/') . '',
                    'description' => 'API Server',
                ]
            ];
            
            $this->info('Added server URL: ' . $serverUrl);
        }
        
        return $spec;
    }
        
    /**
     * Добавляет тэги
     */
    protected function addTags(array $spec): array
    {
        $openapiConfig = $this->config['openapi'] ?? [];
        $tagsConfig = $openapiConfig['tags'] ?? [];
        
        if (!empty($tagsConfig)) {
            if (empty($spec['tags'])) {
                $spec['tags'] = $tagsConfig;
            } else {
                $existingTags = array_column($spec['tags'], 'name');
                foreach ($tagsConfig as $tag) {
                    if (!in_array($tag['name'], $existingTags)) {
                        $spec['tags'][] = $tag;
                    }
                }
            }
            $this->info('Added ' . count($tagsConfig) . ' tags from config');
        }
        
        return $spec;
    }
    
    /**
     * Сохраняет спецификацию в файл
     *
     * @param array $spec
     * @return bool
     */
    protected function saveToFile(array $spec): bool
    {
        $outputPath = $this->config['output_path'] ?? 
            MODX_BASE_PATH . 'assets/modules/swagger-ui/openapi.json';
        
        $json = json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        
        if ($json === false) {
            throw new \Exception('Failed to encode JSON: ' . json_last_error_msg());
        }
        
        $result = File::put($outputPath, $json);
        
        if ($result === false) {
            throw new \Exception('Failed to write file: ' . $outputPath);
        }
        
        $this->info('Documentation saved to: ' . $outputPath);
        $this->info('File size: ' . number_format(strlen($json) / 1024, 2) . ' KB');
        
        return true;
    }
    
    /**
     * Подготавливает директорию для сохранения
     *
     * @return void
     */
    protected function prepareDirectory(): void
    {
        $outputPath = $this->config['output_path'] ?? 
            MODX_BASE_PATH . 'assets/modules/swagger-ui/openapi.json';
        
        $directory = dirname($outputPath);
        
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
            $this->info('Created directory: ' . $directory);
        }
        
        if (!File::isWritable($directory)) {
            throw new \Exception('Directory is not writable: ' . $directory);
        }
    }
    
    /**
     * Логирование информации
     *
     * @param string $message
     * @return void
     */
    protected function info(string $message): void
    {
        if (app()->runningInConsole()) {
            echo "[INFO] " . $message . PHP_EOL;
        }
        Log::info('[Swagger] ' . $message);
    }
    
    /**
     * Логирование предупреждений
     *
     * @param string $message
     * @return void
     */
    protected function warn(string $message): void
    {
        if (app()->runningInConsole()) {
            echo "[WARN] " . $message . PHP_EOL;
        }
        Log::warning('[Swagger] ' . $message);
    }
    
    /**
     * Логирование ошибок
     *
     * @param string $message
     * @return void
     */
    protected function error(string $message): void
    {
        if (app()->runningInConsole()) {
            echo "[ERROR] " . $message . PHP_EOL;
        }
        Log::error('[Swagger] ' . $message);
    }
    
    /**
     * Логирование отладочной информации
     *
     * @param string $message
     * @return void
     */
    protected function debug(string $message): void
    {
        if (app()->runningInConsole() && (env('APP_DEBUG', false))) {
            echo "[DEBUG] " . $message . PHP_EOL;
        }
        Log::debug('[Swagger] ' . $message);
    }
    
    /**
     * Логирование обычного сообщения
     *
     * @param string $message
     * @return void
     */
    protected function line(string $message): void
    {
        if (app()->runningInConsole()) {
            echo $message . PHP_EOL;
        }
    }
    
    /**
     * Проверяет существование сгенерированного файла
     *
     * @return bool
     */
    public function isGenerated(): bool
    {
        $outputPath = $this->config['output_path'] ?? 
            MODX_BASE_PATH . 'assets/modules/swagger-ui/openapi.json';
        
        return File::exists($outputPath);
    }
    
    /**
     * Возвращает путь к сгенерированному файлу
     *
     * @return string|null
     */
    public function getOutputPath(): ?string
    {
        return $this->config['output_path'] ?? null;
    }
    
    /**
     * Возвращает конфигурацию генератора
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}