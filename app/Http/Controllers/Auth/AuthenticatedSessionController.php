<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $token = $request->authenticate();

        if ($token) {
            // MODO Token: Retorna JSON con el token
            return response()->json([
                'token' => $token,
                'message' => 'Autenticación exitosa',
            ]);
        }

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        $user = $request->user();

        // 🔹 Si viene con token (Passport/Sanctum)
        if ($user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
            return response()->noContent();
        }

        // 🔹 Si viene con sesión web (guard web)
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->noContent();
        }

        // 🔹 Si no estaba autenticado
        return response()->noContent();
    }

}
