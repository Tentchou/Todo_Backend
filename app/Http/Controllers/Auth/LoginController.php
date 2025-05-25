<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->authenticate(); // Méthode du trait AuthenticatesUsers

        // Sanctum gère la session et les cookies CSRF
        // Une fois authentifié, Laravel génère un cookie de session
        // et un token CSRF est disponible via /sanctum/csrf-cookie
        // Le frontend n'a pas besoin de "token" à stocker ici, Sanctum le gère.

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout(); // Déconnexion de la session web

        $request->session()->invalidate(); // Invalide la session
        $request->session()->regenerateToken(); // Régénère le token CSRF

        // Revoke all tokens for the user (if using API tokens explicitly)
        // $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
