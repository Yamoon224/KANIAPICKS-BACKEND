<?php

namespace App\Modules\Trading\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PositionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PositionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $positions = $request->user()->positions()->where('quantite', '>', 0)->get();

        return PositionResource::collection($positions);
    }
}
