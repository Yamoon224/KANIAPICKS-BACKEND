<?php

namespace App\Modules\Marches\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarcheResource;
use App\Modules\Marches\Actions\ResoudreMarcheAction;
use App\Modules\Marches\Http\Requests\ResoudreMarcheRequest;
use App\Modules\Marches\Models\Marche;

class ResolutionController extends Controller
{
    public function __construct(
        private readonly ResoudreMarcheAction $resoudreMarche,
    ) {}

    public function store(ResoudreMarcheRequest $request, Marche $marche): MarcheResource
    {
        $this->resoudreMarche->executer(
            marche: $marche,
            issueGagnante: $request->string('issue_gagnante')->value(),
            preuveUrl: $request->string('preuve_url')->value(),
        );

        return new MarcheResource($marche->fresh());
    }
}
