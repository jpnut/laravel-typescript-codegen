<?php

namespace JPNut\CodeGen;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use JPNut\CodeGen\Contracts\InterfaceWriterContract;
use JPNut\CodeGen\Contracts\MethodWriterContract;
use phpDocumentor\Reflection\DocBlockFactory;

class CodeGenServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/typescript-codegen.php' => config_path('typescript-codegen.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/typescript-codegen.php',
            'typescript-codegen'
        );

        $config = config('typescript-codegen.writers');

        $docBlockReader = DocBlockFactory::createInstance(['code-gen' => CodeGenTag::class]);

        $registrar = new TypeRegistrar($docBlockReader);
        $this->app->singleton(TypeRegistrar::class, fn() => $registrar);

        $interfaceWriter = $this->app->make($config['interface']);
        $this->app->bind(InterfaceWriterContract::class, fn() => $interfaceWriter);

        $methodWriter = $this->app->make($config['method']);
        $this->app->bind(MethodWriterContract::class, fn() => $methodWriter);

        $this->app->bind(Generator::class, fn() => new Generator(
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
