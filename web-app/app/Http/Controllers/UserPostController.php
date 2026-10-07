<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;

class UserPostController extends Controller
{
    // หน้าฟอร์มแก้ไขโพสต์ แก้ได้เฉพาะโพสต์ของตัวเอง
    public function edit($id)
    {
        $item = Item::findOrFail($id);

        if ($item->user_id === null || $item->user_id !== Auth::user()->finder_user_id) {
            return redirect()->route('profile.edit')->with('error', 'ไม่สามารถแก้ไขโพสต์ของคนอื่นได้');
        }

        $categories = Category::all();
        $locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');

        return view('user.edit', compact('item', 'categories', 'locations'));
    }

    // บันทึกการแก้ไขโพสต์
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        // เช็คเจ้าของซ้ำอีกรอบ เพราะยิง PUT ตรงมาได้โดยไม่ต้องผ่านหน้าฟอร์ม
        if ($item->user_id === null || $item->user_id !== Auth::user()->finder_user_id) {
            return redirect()->route('profile.edit')->with('error', 'ไม่สามารถแก้ไขโพสต์ของคนอื่นได้');
        }

        // กฎเดียวกับตอนสร้างโพสต์ใน ItemController@store
        $validated = $request->validate([
            'postType' => ['required', 'in:found,lost'],
            'itemName' => ['required', 'string', 'max:255'],
            'category' => ['required', 'exists:categories,id'],
            'location' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'reporterName' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // ถ้าไม่ได้เลือกรูปใหม่ ใช้รูปเดิมต่อ
        if ($request->hasFile('image')) {
            $item->image_url = 'storage/' . $request->file('image')->store('items', 'public');
        }

        $item->type = $validated['postType'];
        $item->title = $validated['itemName'];
        $item->category_id = $validated['category'];
        $item->location = $validated['location'];
        $item->event_date = $validated['date'];
        $item->description = $validated['description'];
        $item->reporter_name = $validated['reporterName'];
        $item->reporter_phone = $validated['phone'];
        // แก้เนื้อหาแล้วต้องให้แอดมินตรวจใหม่
        $item->approval_status = 'รออนุมัติ';
        $item->save();

        return redirect()->route('profile.edit')->with('success', 'แก้ไขโพสต์ ' . $item->title . ' เรียบร้อยแล้ว');
    }
}
