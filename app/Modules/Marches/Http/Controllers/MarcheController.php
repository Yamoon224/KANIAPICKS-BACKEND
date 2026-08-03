<?php

namespace App\Modules\Marches\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarcheResource;
use App\Modules\Marches\Http\Requests\StoreMarcheRequest;
use App\Modules\Marches\Models\Marche;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MarcheController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $marches = Marche::query()
            ->when($request->string('statut')->isNotEmpty(), fn ($q) => $q->where('statut', $request->string('statut')))
            ->when(! $request->has('statut'), fn ($q) => $q->where('statut', 'publie'))
            ->when($request->string('categorie')->isNotEmpty(), fn ($q) => $q->where('categorie', $request->string('categorie')))
            ->orderByDesc('created_at')
            ->paginate(20);

        return MarcheResource::collection($marches);
    }

    public function show(Marche $marche): MarcheResource
    {
        return new MarcheResource($marche);
    }

    public function store(StoreMarcheRequest $request): MarcheResource
    {
        $marche = Marche::create([
            ...$request->validated(),
            'statut' => 'publie',
            'valeur_nominale_cents' => $request->integer('valeur_nominale_cents') ?: config('kaniapicks.valeur_nominale_part_cents'),
            'liquidite_b' => $request->integer('liquidite_b') ?: 1000,
        ]);

        return new MarcheResource($marche);
    }
}
