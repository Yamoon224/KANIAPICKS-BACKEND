<?php

namespace App\Providers;

use App\Contracts\Encaissable;
use App\Contracts\Notifiable;
use App\Contracts\Versable;
use App\Modules\Paiements\Contracts\PasserellePaiementContract;
use App\Modules\Paiements\Gateways\CinetPayGateway;
use App\Modules\Portefeuille\Services\LedgerService;
use App\Modules\Trading\Contracts\MoteurCotationContract;
use App\Modules\Trading\Services\AmmMoteurCotationService;
use App\Services\NotificationDispatcherService;
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
        //
    }
}
