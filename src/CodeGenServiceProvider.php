<?php

namespace JPNut\CodeGen;

use Illuminate\Routing\Router;
use JPNut\CodeGen\Commands\Generate;
use Illuminate\Support\ServiceProvider;
use phpDocumentor\Reflection\DocBlockFactory;
use JPNut\CodeGen\Contracts\MethodWriterContract;
use Illuminate\Contracts\Support\DeferrableProvider;
use JPNut\CodeGen\Contracts\InterfaceWriterContract;

class CodeGenServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/typescript-codegen-publish.php' => config_path('typescript-codegen.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/stubs/typescript-codegen-schema.stub' => resource_path('stubs/typescript-codegen-schema.stub'),
        ], 'config');

        $this->commands([
            Generate::class,
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/typescript-codegen.php',
            'typescript-codegen'
        );

        $config = config('typescript-codegen');

        $docBlockReader = DocBlockFactory::createInstance(['code-gen' => CodeGenTag::class]);

        $registrar = new TypeRegistrar($docBlockReader, $config['request_properties']);
        $this->app->singleton(TypeRegistrar::class, fn () => $registrar);

        $interfaceWriter = $this->app->make($config['writers']['interface']);
        $this->app->bind(InterfaceWriterContract::class, fn () => $interfaceWriter);

        $methodWriter = $this->app->make($config['writers']['method']);
        $this->app->bind(MethodWriterContract::class, fn () => $methodWriter);

        $this->app->bind(Generator::class, fn () => new Generator(
            $this->app->make(Router::class),
            $registrar,
            $interfaceWriter,
            $methodWriter,
            $docBlockReader
        ));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            TypeRegistrar::class,
            InterfaceWriterContract::class,
            MethodWriterContract::class,
            Generator::class,
        ];
    }
}
