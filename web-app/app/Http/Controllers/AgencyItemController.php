<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\FinderUser;

class AgencyItemController extends Controller
{
    // ตอนนี้ระบบ login ยังเป็นงานของอีกส่วนหนึ่ง เลยเก็บ id ของหน่วยงานที่กำลังใช้งานไว้ใน session ก่อน
    private function currentAgency()
    {
        $id = session('finder_user_id');

        if ($id === null) {
            return null;
        }

        $user = FinderUser::find($id);

        // กันกรณีที่ session ค้างไว้แต่ user ถูกลบไปแล้ว หรือไม่ใช่หน่วยงาน
        if ($user === null || $user->role !== 'agency') {
            return null;
        }

        return $user;
    }

    // เลือกว่าจะใช้งานในนามหน่วยงานไหน (ใช้แทนหน้า login ชั่วคราว)
    public function switchUser(Request $request)
    {
        $validated = $request->validate([
            'finder_user_id' => ['required', 'integer'],
        ]);

        $user = FinderUser::find($validated['finder_user_id']);

        if ($user === null || $user->role !== 'agency') {
            return redirect()->route('agency.index')->with('error', 'ไม่พบบัญชีหน่วยงานนี้');
        }

        session(['finder_user_id' => $user->id]);

        return redirect()->route('agency.index')->with('success', 'เข้าใช้งานในนาม ' . $user->fullname . ' แล้ว');
    }

    // รายการโพสต์ทั้งหมดของหน่วยงานตัวเอง
    public function index()
    {
        $agency = $this->currentAgency();
        $agencies = FinderUser::where('role', 'agency')->orderBy('id')->get();

        $items = collect();

        if ($agency !== null) {
            $items = Item::where('user_id', $agency->id)
                ->orderBy('id', 'DESC')
                ->paginate(5);

            foreach ($items as $item) {
                $itemCategory = Category::find($item->category_id);
                $item->category_name = $itemCategory ? $itemCategory->name : 'อื่นๆ';
            }
        }

        return view('agency.index', compact('agency', 'agencies', 'items'));
    }

    // หน้าฟอร์มโพสต์ของที่เก็บได้
    public function create()
    {
        $agency = $this->currentAgency();

        if ($agency === null) {
            return redirect()->route('agency.index')->with('error', 'กรุณาเลือกบัญชีหน่วยงานก่อนโพสต์');
        }

        $categories = Category::all();
        $locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');

        return view('agency.create', compact('agency', 'categories', 'locations'));
    }

    public function store(Request $request)
{
    $agency = $this->currentAgency();

    if ($agency === null) {
        return redirect()->route('agency.index')->with('error', 'กรุณาเลือกบัญชีหน่วยงานก่อนโพสต์');
    }

    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'category_id' => ['required', 'integer'],
        'type' => ['required', 'string'],
        'description' => ['nullable', 'string'],
        'location' => ['required', 'string', 'max:255'],
        'place_point' => ['required', 'string', 'max:255'],
        'event_date' => ['required', 'date'],
        'image_url' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        'evidence_url' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        'evidence_note' => ['required', 'string'],
    ]);

    // หมวดหมู่ต้องมีอยู่จริงในตาราง categories
    if (Category::find($validated['category_id']) === null) {
        return redirect()->back()->withInput()->with('error', 'ไม่พบหมวดหมู่ที่เลือก');
    }

    if ($validated['type'] !== 'found' && $validated['type'] !== 'lost') {
        return redirect()->back()->withInput()->with('error', 'ประเภทของประกาศไม่ถูกต้อง');
    }

    $imagePath = null;
    if ($request->hasFile('image_url')) {
        $imagePath = 'storage/' . $request->file('image_url')->store('items', 'public');
    }

    $evidencePath = 'storage/' . $request->file('evidence_url')->store('evidence', 'public');

    $item = new Item;
    $item->user_id = $agency->id;
    $item->category_id = $validated['category_id'];
    $item->type = $validated['type'];
    $item->title = $validated['title'];
    $item->description = $validated['description'];
    $item->location = $validated['location'];
    $item->place_point = $validated['place_point'];
    $item->event_date = $validated['event_date'];
    $item->image_url = $imagePath;
    $item->evidence_url = $evidencePath;
    $item->evidence_note = $validated['evidence_note'];
    $item->status = 'ยังไม่พบเจ้าของ';
    // โพสต์ของหน่วยงานต้องรอแอดมินอนุมัติก่อนถึงจะขึ้นหน้าเว็บ
    $item->approval_status = 'รออนุมัติ';
    $item->save();

    return redirect()->route('agency.show', $item->id)->with('success', 'ส่งโพสต์ให้แอดมินตรวจสอบแล้ว');
}
}
