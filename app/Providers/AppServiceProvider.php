<?php

namespace App\Providers;

use App\Contracts\Encaissable;
use App\Contracts\Notifiable;
use App\Contracts\Versable;
use App\Models\User;
use App\Modules\Paiements\Contracts\PasserellePaiementContract;
use App\Modules\Paiements\Gateways\CinetPayGateway;
use App\Modules\Portefeuille\Services\LedgerService;
use App\Modules\Trading\Contracts\MoteurCotationContract;
use App\Modules\Trading\Services\AmmMoteurCotationService;
use App\Services\NotificationDispatcherService;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LedgerService::class);
        $this->app->bind(Encaissable::class, LedgerService::class);
        $this->app->bind(Versable::class, LedgerService::class);
        $this->app->bind(Notifiable::class, NotificationDispatcherService::class);
        $this->app->bind(MoteurCotationContract::class, AmmMoteurCotationService::class);
        $this->app->bind(PasserellePaiementContract::class, CinetPayGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sert la doc API (Swagger/OpenAPI, via Scramble) à la racine du site,
        // et le spec JSON sur /docs/api.json.
        Scramble::configure()->expose(
            ui: '/',
            document: 'docs/api.json',
        );

        // La doc API est publique, y compris en production : Scramble restreint
        // l'accès aux environnements non-`local` sauf si cette Gate autorise.
        Gate::define('viewApiDocs', fn (?User $user = null) => true);
    }
}
