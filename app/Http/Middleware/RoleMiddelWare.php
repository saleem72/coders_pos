<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Role;
use Illuminate\Http\Request;
use App\DataTransfareObjects\V1\CustomJson;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddelWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(401);
            // return response()->json(new CustomJson(status: false, message: 'Un authorized', data: Null), 401);
        }
        $role_id = 3;
        if ($role == 'superAdmin') {
            $role_id = 1;
        } else if ($role == 'admin') {
            $role_id = 2;
        }
        error_log('User role_id: '.auth()->user()->role_id.' '.$role_id );
        if (auth()->user()->role_id != $role_id) {
            // abort(403, message: 'Un authorized Admin');
            return response()->json(new CustomJson(status: false, message: 'Un authorized Admin', data: Null), 403);
        }


        return $next($request);
    }
}
