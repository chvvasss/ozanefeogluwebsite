<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Writing;
use App\Policies\WritingPolicy;
use App\Services\HibpService;
use App\Services\SettingsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsRepository::class);
        $this->app->bind(HibpService::class, fn () => HibpService::fromConfig());
    }

    public function boot(): void
    {
        Gate::policy(Writing::class, WritingPolicy::class);

        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventAccessingMissingAttributes(! $this->app->isProduction());

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Blade::directive('cspNonce', function (): string {
            return "<?php echo e(request()->attributes->get('csp_nonce', '')); ?>";
        });
    }
}
