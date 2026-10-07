<?php

namespace App\Http\Controllers;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    use PasswordValidationRules;

    public function create(): View
    {
        return view('user.forgot-password');
    }

    // ตั้งรหัสใหม่ได้เลยถ้าอีเมลกับชื่อตรงกับบัญชี (ไม่ส่งอีเมล)
    // ข้อควรระวัง: ใครรู้อีเมลกับชื่อของเจ้าของบัญชีก็เปลี่ยนรหัสได้ เลยจำกัดจำนวนครั้งด้วย throttle ใน route
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || mb_strtolower(trim($user->name)) !== mb_strtolower(trim($validated['name']))) {
            return back()->withInput($request->only('email', 'name'))
                ->withErrors(['email' => 'อีเมลหรือชื่อไม่ตรงกับบัญชีใดในระบบ']);
        }

        $user->update(['password' => $validated['password']]);

        return redirect()->route('login')->with('status', 'ตั้งรหัสผ่านใหม่แล้ว เข้าสู่ระบบด้วยรหัสใหม่ได้เลย');
    }
}
