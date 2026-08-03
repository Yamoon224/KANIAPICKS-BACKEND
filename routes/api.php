<?php

use App\Http\Controllers\Auth\AuthController;
use App\Modules\Marches\Http\Controllers\MarcheController;
use App\Modules\Marches\Http\Controllers\ResolutionController;
use App\Modules\Paiements\Http\Controllers\CinetPayWebhookController;
use App\Modules\Paiements\Http\Controllers\DepotController;
use App\Modules\Portefeuille\Http\Controllers\PortefeuilleController;
use App\Modules\Trading\Http\Controllers\OrdreController;
use App\Modules\Trading\Http\Controllers\PositionController;
use Illuminate\Support\Facades\Route;

Route::get('/up', fn () => response()->json(['status' => 'ok']));

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/webhooks/cinetpay', [CinetPayWebhookController::class, 'handle']);

Route::get('/marches', [MarcheController::class, 'index']);
Route::get('/marches/{marche}', [MarcheController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/portefeuille', [PortefeuilleController::class, 'show']);

    Route::get('/positions', [PositionController::class, 'index']);

    Route::get('/ordres', [OrdreController::class, 'index']);
    Route::post('/marches/{marche}/ordres', [OrdreController::class, 'store']);

    Route::get('/depots', [DepotController::class, 'index']);
    Route::post('/depots', [DepotController::class, 'store']);

    Route::middleware('role:editeur,admin')->group(function () {
        Route::post('/marches', [MarcheController::class, 'store']);
    });

    Route::middleware('role:agent_resolution,admin')->group(function () {
        Route::post('/marches/{marche}/resolution', [ResolutionController::class, 'store']);
    });
});
