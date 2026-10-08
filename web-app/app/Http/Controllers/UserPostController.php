<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\ReturnUnit;

class UserPostController extends Controller
{
    // หน้าฟอร์มแก้ไขโพสต์ แก้ได้เฉพาะโพสต์ของตัวเอง
    public function edit($id)
    {
        $item = Item::findOrFail($id);

        if ($item->user_id === null || $item->user_id !== Auth::user()->id) {
            return redirect()->route('profile.edit')->with('error', 'ไม่สามารถแก้ไขโพสต์ของคนอื่นได้');
        }

        // ส่งหลักฐานการคืนแล้วแก้ไขไม่ได้
        if (! $item->canEdit()) {
            return redirect()->route('profile.edit')->with('error', 'โพสต์ ' . $item->title . ' ส่งหลักฐานการคืนแล้ว แก้ไขไม่ได้');
        }

        $categories = Category::all();
        // autocomplete สถานที่ใช้ชื่อจุดรับ-ส่งคืน (return_units)
        $locations = ReturnUnit::orderBy('name')->pluck('name');

        return view('user.edit', compact('item', 'categories', 'locations'));
    }

    // บันทึกการแก้ไขโพสต์
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        // เช็คเจ้าของซ้ำอีกรอบ เพราะยิง PUT ตรงมาได้โดยไม่ต้องผ่านหน้าฟอร์ม
        if ($item->user_id === null || $item->user_id !== Auth::user()->id) {
            return redirect()->route('profile.edit')->with('error', 'ไม่สามารถแก้ไขโพสต์ของคนอื่นได้');
        }

        if (! $item->canEdit()) {
            return redirect()->route('profile.edit')->with('error', 'โพสต์ ' . $item->title . ' ส่งหลักฐานการคืนแล้ว แก้ไขไม่ได้');
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
            'phone' => ['nullable', 'string', 'max:12', 'regex:/^0[0-9]{2}-?[0-9]{3}-?[0-9]{4}$/'],
        ], [
            'phone.regex' => 'กรุณากรอกเบอร์โทร 10 หลัก ขึ้นต้นด้วย 0 เช่น 081-234-5678',
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
        $item->save();

        return redirect()->route('profile.edit')->with('success', 'แก้ไขโพสต์ ' . $item->title . ' เรียบร้อยแล้ว');
    }

    // ส่งหลักฐานการส่งคืน: เจ้าของโพสต์อัปโหลดรูป แล้วสถานะเปลี่ยนเป็น "รอแอดมินยืนยัน"
    public function submitEvidence(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        if ($item->user_id === null || $item->user_id !== Auth::user()->id) {
            return redirect()->route('profile.edit')->with('error', 'ไม่สามารถส่งหลักฐานของโพสต์คนอื่นได้');
        }

        if (! $item->canSubmitEvidence()) {
            return redirect()->route('profile.edit')->with('error', 'โพสต์ ' . $item->title . ' ยังส่งหลักฐานไม่ได้');
        }

        $validated = $request->validate([
            'evidence' => ['required', 'mimes:jpeg,png,jpg,gif,webp,avif', 'max:2048'],
            'evidence_note' => ['nullable', 'string', 'max:255'],
        ]);

        $item->evidence_url = 'storage/' . $request->file('evidence')->store('evidence', 'public');
        $item->evidence_note = $validated['evidence_note'] ?? null;
        $item->status = 'รอแอดมินยืนยัน';
        $item->save();

        return redirect()->route('profile.edit')->with('success', 'ส่งหลักฐานของ ' . $item->title . ' แล้ว รอแอดมินยืนยัน');
    }

    // ลบโพสต์ของตัวเอง ลบไม่ได้หลังส่งหลักฐานแล้ว (ล็อกเหมือนการแก้ไข)
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        if ($item->user_id === null || $item->user_id !== Auth::user()->id) {
            return redirect()->route('profile.edit')->with('error', 'ไม่สามารถลบโพสต์ของคนอื่นได้');
        }

        if (! $item->canEdit()) {
            return redirect()->route('profile.edit')->with('error', 'โพสต์ ' . $item->title . ' ส่งหลักฐานการคืนแล้ว ลบไม่ได้');
        }

        $item->deleteWithFiles();

        return redirect()->route('profile.edit')->with('success', 'ลบโพสต์ ' . $item->title . ' แล้ว');
    }
}
