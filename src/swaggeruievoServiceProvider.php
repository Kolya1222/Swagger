<?php 

namespace roilafx\swaggeruievo;

use EvolutionCMS\ServiceProvider;
use roilafx\swaggeruievo\Console\Commands\GenerateSwaggerDocsCommand;
use roilafx\swaggeruievo\Services\SwaggerGenerator;

class swaggeruievoServiceProvider extends ServiceProvider
{
    protected $namespace = 'swaggeruievo';

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/swagger-ui.php', 'swagger-ui');
        
        $this->app->singleton(SwaggerGenerator::class, function () {
            return new SwaggerGenerator();
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateSwaggerDocsCommand::class,
            ]);
        }
    }

    public function boot() : void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'swagger-ui');

        $this->publishes([
            __DIR__ . '/../publishable/assets'  => MODX_BASE_PATH . 'assets',
        ], 'assets');
        
        $this->app->registerRoutingModule(
            'Swagger UI Documentation',
            __DIR__ . '/../routes/module.php',
            'fa fa-file-code-o'
        );
    }
}