<?php

namespace App\Modules\Paiements\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepotResource;
use App\Modules\Paiements\Http\Requests\InitierDepotRequest;
use App\Modules\Paiements\Services\InitierDepotService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DepotController extends Controller
{
    public function __construct(
        private readonly InitierDepotService $initierDepot,
    ) {}

    public function store(InitierDepotRequest $request): DepotResource
    {
        $depot = $this->initierDepot->initier(
            user: $request->user(),
            montantCents: $request->integer('montant_cents'),
            numeroMobileMoney: $request->string('numero_mobile_money')->value(),
            operateur: $request->string('operateur')->value(),
        );

        return new DepotResource($depot);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $depots = $request->user()->depots()->orderByDesc('created_at')->paginate(20);

        return DepotResource::collection($depots);
    }
}
