<?php

namespace App\Providers;

use App\Utilities\AngularAssetsFetcher;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootstrapCustomBladeDirectives();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    private function bootstrapCustomBladeDirectives()
    {
        // Script directive
        $this->registerNgScriptBladeDirective();

        // Style directive
        $this->registerNgStyleBladeDirective();

        // Production directive
        $this->registerProductionBladeDirective();

        // Local directive
        $this->registerLocalBladeDirective();
    }

    private function registerNgScriptBladeDirective()
    {
        Blade::directive('ngScript', function ($prefix) {
            $src = app(AngularAssetsFetcher::class)->$prefix;

            return "<script type=\"text/javascript\" src=\"dist/{$src}\"></script>";
        });
    }

    private function registerNgStyleBladeDirective()
    {
        Blade::directive('ngStyle', function ($prefix) {
            $src = app(AngularAssetsFetcher::class)->$prefix;

            return "<link rel=\"stylesheet\" href=\"dist/{$src}\">";
        });
    }

    private function registerProductionBladeDirective()
    {
        Blade::directive('prod', function () {
            return "<?php if(app()->environment('production')): ?>";
        });

        Blade::directive('endprod', function () {
            return "<?php endif; ?>";
        });
    }

    private function registerLocalBladeDirective()
    {
        Blade::directive('local', function () {
            return "<?php if(app()->environment('local')): ?>";
        });

        Blade::directive('endlocal', function () {
            return "<?php endif; ?>";
        });
    }
}
