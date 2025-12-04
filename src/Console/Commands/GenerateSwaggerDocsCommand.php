<?php

namespace roilafx\Swagger\Console\Commands;

use Illuminate\Console\Command;
use roilafx\Swagger\Services\SwaggerGenerator;

class GenerateSwaggerDocsCommand extends Command
{
    protected $signature = 'swagger:generate
                            {--force : Overwrite existing file}
                            {--clear : Clear cache before generation}';
    
    protected $description = 'Generate OpenAPI documentation from annotations';

    public function handle(SwaggerGenerator $generator)
    {
        $config = config('swagger-ui.generator', []);
        
        if (!($config['enabled'] ?? true)) {
            $this->error('Swagger generator is disabled in configuration.');
            return 1;
        }
        
        $this->info('Generating OpenAPI documentation...');
        
        if ($this->option('clear')) {
            $this->call('cache:clear');
            $this->info('Cache cleared.');
        }
        
        try {
            if ($generator->generate()) {
                $outputPath = $config['output_path'] ?? 
                    MODX_BASE_PATH . 'assets/modules/swagger-ui/openapi.json';
                
                $this->info('Documentation generated successfully!');
                $this->line('File: ' . $outputPath);
                
                // Проверяем размер файла
                if (file_exists($outputPath)) {
                    $size = filesize($outputPath);
                    $this->line('Size: ' . number_format($size / 1024, 2) . ' KB');
                }
                
                return 0;
            } else {
                $this->error('Failed to generate documentation.');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}