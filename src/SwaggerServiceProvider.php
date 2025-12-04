<?php 

namespace roilafx\Swagger;

use EvolutionCMS\ServiceProvider;
use roilafx\Swagger\Console\Commands\GenerateSwaggerDocsCommand;
use roilafx\Swagger\Services\SwaggerGenerator;

class SwaggerServiceProvider extends ServiceProvider
{
    protected $namespace = 'swagger';

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