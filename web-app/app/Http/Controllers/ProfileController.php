<?php

namespace App\Http\Controllers;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Category;
use App\Models\Item;

class ProfileController extends Controller
{
    use PasswordValidationRules, ProfileValidationRules;

    public function edit(Request $request): View
    {
        $user = $request->user();

        // โพสต์ของฉัน: แสดงให้ทุก role ที่ผูกกับ finder_users (แอดมินก็โพสต์เองได้เหมือนกัน)
        $items = collect();
        $categories = collect();
        $locations = collect();
        $statuses = collect();

        if ($user->finder_user_id !== null) {
            $query = Item::with('category')->where('user_id', $user->finder_user_id);
            $query = $this->filter($query, $request);
            $items = $query->orderBy('id', 'DESC')->get();

            $categories = Category::all();
            $locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');
            $statuses = Item::select('status')->distinct()->orderBy('status')->pluck('status');
        }

        $type = $request->input('type') ?? '';
        $status = $request->input('status') ?? '';
        $category = $request->input('category') ?? '';
        $keyword = trim($request->input('keyword') ?? '');
        $location = trim($request->input('location') ?? '');
        $start_date = $request->input('start_date') ?? '';
        $end_date = $request->input('end_date') ?? '';

        return view('user.profile', compact(
            'user',
            'items',
            'categories',
            'locations',
            'statuses',
            'type',
            'status',
            'category',
            'keyword',
            'location',
            'start_date',
            'end_date'
        ));
    }

    // ใส่เงื่อนไข where ให้ query ของ "โพสต์ของฉัน" ตามช่องที่กรอกจริง (ไม่ใช่ค่าว่าง)
    private function filter($query, Request $request)
    {
        $keyword = trim($request->input('keyword') ?? '');
        $type = $request->input('type') ?? '';
        $status = $request->input('status') ?? '';
        $category = $request->input('category') ?? '';
        $location = trim($request->input('location') ?? '');
        $start_date = $request->input('start_date') ?? '';
        $end_date = $request->input('end_date') ?? '';

        if ($keyword !== '') {
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        if ($type !== '') {
            $query->where('type', $type);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($category !== '') {
            $query->where('category_id', $category);
        }

        if ($location !== '') {
            $query->where('location', 'like', '%' . $location . '%');
        }

        if ($start_date !== '') {
            $query->where('event_date', '>=', $start_date);
        }

        if ($end_date !== '') {
            $query->where('event_date', '<=', $end_date);
        }

        return $query;
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
