<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika pengguna sudah login dan rolenya adalah 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Jika benar, izinkan akses
            return $next($request);
        }

        // Jika bukan admin, cek rolenya apa
        if (Auth::check() && Auth::user()->role === 'petugas') {
            // Jika dia petugas, kembalikan ke dashboard petugas tanpa pesan error
            return redirect()->route('pencatatan.index');
        }

        // Jika pengguna tidak login sama sekali atau role tidak terdefinisi,
        // kembalikan ke halaman login.
        return redirect('/login');
    }
}