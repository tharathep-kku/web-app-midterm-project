<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBanned
{
    // บัญชีที่ถูกระงับ: ออกจากระบบทันทีทุกหน้า (รวมถึงคนที่ล็อกอินค้างไว้ก่อนโดนระงับ)
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_banned) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'บัญชีนี้ถูกระงับการใช้งาน กรุณาติดต่อแอดมิน']);
        }

        return $next($request);
    }
}
