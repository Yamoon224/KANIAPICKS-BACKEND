<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Modules\Conformite\Models\ProfilKyc;
use App\Modules\Portefeuille\Models\Portefeuille;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Authentification par e-mail/mot de passe avec jetons Sanctum. La
 * vérification par OTP SMS (cf. cahier des charges, section 5.1) nécessite
 * une passerelle SMS non configurée dans ce scaffold ; à ajouter derrière
 * le même contrôleur sans changer le contrat côté frontend.
 */
class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->string('name'),
                'email' => $request->string('email'),
                'password' => Hash::make($request->string('password')),
            ]);

            Portefeuille::create([
                'user_id' => $user->id,
                'devise' => config('kaniapicks.devise_defaut'),
            ]);

            ProfilKyc::create([
                'user_id' => $user->id,
                'palier' => 1,
                'statut' => 'non_soumis',
                'plafond_depot_journalier_cents' => config('kaniapicks.plafonds_kyc.1.depot'),
                'plafond_retrait_journalier_cents' => config('kaniapicks.plafonds_kyc.1.retrait'),
            ]);

            return $user;
        });

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Identifiants invalides.'],
            ]);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
