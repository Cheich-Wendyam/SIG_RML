<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Débogage : vérifier le rôle
            \Log::info('User role: ' . $user->role);

            if ($user->role === 'admin') {
                return $next($request);
            }
        }

        else {
            // Si l'utilisateur n'est pas connecté, retourne une réponse 401 Unauthorized
            return response()->json(['message' => 'Unauthorized'], 401);
        }

    }
}
