<?php

namespace App\Http\Controllers;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use PasswordValidationRules, ProfileValidationRules;

    public function edit(Request $request): View
    {
        return view('user.profile', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->update($request->validate($this->profileRules($user->id)));

        return back()->with('success', 'บันทึกข้อมูลโปรไฟล์แล้ว');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return back()->with('success', 'เปลี่ยนรหัสผ่านแล้ว');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['delete_password' => ['required', 'current_password']]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
