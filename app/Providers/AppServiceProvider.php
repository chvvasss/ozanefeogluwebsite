<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Photo;
use App\Models\Publication;
use App\Models\Setting;
use App\Models\User;
use App\Models\Writing;
use App\Policies\BackupPolicy;
use App\Policies\ContactMessagePolicy;
use App\Policies\PagePolicy;
use App\Policies\PhotoPolicy;
use App\Policies\PublicationPolicy;
use App\Policies\UserPolicy;
use App\Policies\WritingPolicy;
use App\Services\HibpService;
use App\Support\SettingsRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HibpService::class, fn () => HibpService::fromConfig());
    }

    public function boot(): void
    {
        Gate::policy(Writing::class, WritingPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(Photo::class, PhotoPolicy::class);
        Gate::policy(Publication::class, PublicationPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);
        Gate::define('manage-backups', [BackupPolicy::class, 'manage']);

        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventAccessingMissingAttributes(! $this->app->isProduction());

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // CSP nonce echo — used in inline <script nonce="@cspNonce"> on production
        Blade::directive('cspNonce', function (): string {
            return "<?php echo e(request()->attributes->get('csp_nonce', '')); ?>";
        });

        // Site-wide setting echo:
        //   @setting('identity.name')            → echoes resolved value
        //   @setting('identity.name', 'Default') → echoes with fallback
        Blade::directive('setting', function (string $expression): string {
            return "<?php echo e(\\App\\Support\\SettingsRepository::get({$expression})); ?>";
        });

        // Invalidate setting cache when admin updates a row.
        Setting::saved(static fn (Setting $s) => SettingsRepository::forget($s->key));
        Setting::deleted(static fn (Setting $s) => SettingsRepository::forget($s->key));
    }
}
