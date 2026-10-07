<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAgency
{
    // ให้เข้าได้เฉพาะบัญชีที่ role เป็น agency เท่านั้น
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->role !== 'agency') {
            return redirect()
                ->route('home')
                ->with('error', 'หน้านี้สำหรับบัญชีหน่วยงานเท่านั้น');
        }

        return $next($request);
    }
}
