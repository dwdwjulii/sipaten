<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekRolePetugas
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Cek apakah pengguna sudah login DAN memiliki role 'petugas'
        if (auth()->check() && auth()->user()->role === 'petugas') {
            // 2. Jika ya, izinkan akses ke halaman berikutnya
            return $next($request);
        }

        // Jika bukan petugas, redirect ke dashboard tanpa pesan error
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect('/dashboard');
        }

        // 3. Jika tidak, arahkan kembali (misalnya ke dashboard admin atau halaman login)
        //    dengan pesan error.
        return redirect('/');
    }
}