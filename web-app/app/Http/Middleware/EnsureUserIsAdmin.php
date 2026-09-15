<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    // ให้เข้าได้เฉพาะบัญชีที่ role เป็น admin เท่านั้น
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->role !== 'admin') {
            return redirect()
                ->route('home')
                ->with('error', 'หน้านี้สำหรับบัญชีแอดมินเท่านั้น');
        }

        return $next($request);
    }
}
