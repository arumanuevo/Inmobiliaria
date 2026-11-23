<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Hosting compartido SIN worker de cola (no se puede correr queue:work permanente):
        // forzamos la cola 'sync' para que mails/notificaciones (verificación de email, alertas,
        // avisos) se envíen EN EL MISMO REQUEST. Si algún día se configura un cron con
        // queue:work, se puede quitar esta línea.
        config(['queue.default' => 'sync']);

        // @dinero($x) / @dinero($x, 0) -> $1.234.567,89 (formato AR: miles con punto, decimales con coma)
        // OJO: Blade le pasa la expresión SIN los paréntesis externos -> hay que envolverla
        \Illuminate\Support\Facades\Blade::directive('dinero', function ($expr) {
            return "<?php echo \\App\\Support\\Dinero::fmt({$expr}); ?>";
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        Gate::define('gestionar-inmobiliarias', fn ($user) => $user->hasRole('admin'));
        Gate::define('gestionar-inmuebles', fn ($user) => $user->hasAnyRole(['admin', 'inmobiliaria', 'particular']));

        // Email de contacto del portal, disponible en todas las vistas (footers, etc.)
        view()->composer('*', function ($view) {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';
            $view->with('mailContacto', config('mail.from.address') ?: 'contacto@' . $host);
        });
    }
}
