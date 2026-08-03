<?php

namespace App\Modules\Trading\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrdreResource;
use App\Modules\Marches\Models\Marche;
use App\Modules\Trading\Actions\ExecuterOrdreAction;
use App\Modules\Trading\Http\Requests\PlacerOrdreRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrdreController extends Controller
{
    public function __construct(
        private readonly ExecuterOrdreAction $executerOrdre,
    ) {}

    public function store(PlacerOrdreRequest $request, Marche $marche): OrdreResource
    {
        $ordre = $this->executerOrdre->executer(
            user: $request->user(),
            marche: $marche,
            issue: $request->string('issue')->value(),
            sens: $request->string('sens')->value(),
            quantite: $request->integer('quantite'),
            slippageMaxPct: $request->integer('slippage_max_pct') ?: config('kaniapicks.slippage_max_pct_defaut'),
        );

        return new OrdreResource($ordre);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $ordres = $request->user()->ordres()->orderByDesc('created_at')->paginate(20);

        return OrdreResource::collection($ordres);
    }
}
