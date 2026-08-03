<?php

namespace App\Modules\Portefeuille\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortefeuilleResource;
use Illuminate\Http\Request;

class PortefeuilleController extends Controller
{
    public function show(Request $request): PortefeuilleResource
    {
        return new PortefeuilleResource($request->user()->portefeuille);
    }
}
