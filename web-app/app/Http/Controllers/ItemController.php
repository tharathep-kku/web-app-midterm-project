<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    // หน้าแรก: แสดงรายการทันที (ไม่ต้องค้นหาก่อน) กรองตามวันที่ได้ และซ่อนของที่คืนเจ้าของเกิน 6 เดือน
    public function home(Request $request): View
    {
        $from = $request->input('from') ?? '';
        $to = $request->input('to') ?? '';
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));

        $query = Item::query()->where(function ($q) use ($sixMonthsAgo) {
            $q->where('status', '!=', 'ได้รับคืนแล้ว')
                ->orWhereNull('returned_date')
                ->orWhere('returned_date', '>', $sixMonthsAgo);
        });

        if ($from !== '') {
            $query->where('event_date', '>=', $from);
        }

        if ($to !== '') {
            $query->where('event_date', '<=', $to);
        }

        $items = $query->with(['category', 'reporter'])
            ->orderBy('event_date', 'desc')
            ->paginate(5)
            ->withQueryString();

        $this->addNames($items);

        return view('home', compact('items', 'from', 'to'));
    }

    // คลังประกาศ: ของที่คืนเจ้าของไปแล้วเกิน 6 เดือน
    public function archive(): View
    {
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));

        $items = Item::where('status', 'ได้รับคืนแล้ว')
            ->whereNotNull('returned_date')
            ->where('returned_date', '<=', $sixMonthsAgo)
            ->with(['category', 'reporter'])
            ->orderBy('returned_date', 'desc')
            ->paginate(5);

        $this->addNames($items);

        return view('archive', compact('items'));
    }

    // ตาราง home/archive ใช้ category_name กับ reporter_name
    private function addNames($items): void
    {
        foreach ($items as $item) {
            $item->category_name = $item->category->name ?? 'อื่นๆ';
            $item->reporter_name = $item->reporter->fullname ?? ($item->reporter_name ?: 'ไม่ทราบชื่อ');
        }
    }

    public function search(Request $request): View
    {
        $searched = $request->has('searched');

        $item_name = trim($request->input('item_name') ?? '');
        $category = $request->input('category') ?? '';
        $location = trim($request->input('location') ?? '');
        $description = trim($request->input('description') ?? '');
        $start_date = $request->input('start_date') ?? '';
        $end_date = $request->input('end_date') ?? '';

        $items = collect();

        if ($searched) {
            $query = Item::query();

            if ($item_name !== '') {
                $query->where('title', 'like', '%' . $item_name . '%');
            }

            if ($category !== '') {
                $query->where('category_id', $category);
            }

            if ($location !== '') {
                $query->where('location', 'like', '%' . $location . '%');
            }

            if ($description !== '') {
                $query->where('description', 'like', '%' . $description . '%');
            }

            if ($start_date !== '') {
                $query->where('event_date', '>=', $start_date);
            }

            if ($end_date !== '') {
                $query->where('event_date', '<=', $end_date);
            }

            $items = $query->with(['category', 'reporter'])
                ->orderBy('event_date', 'desc')
                ->paginate(5)
                ->withQueryString();
        }

        $categories = Category::all();
        $locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');

        return view('search', compact(
            'searched',
            'items',
            'categories',
            'locations',
            'item_name',
            'category',
            'location',
            'description',
            'start_date',
            'end_date'
        ));
    }
    public function show(Item $item): View
    {
        $item->load(['category', 'reporter', 'returnUnit']);

        return view('item', compact('item'));
    }
    // เปิดหน้าฟอร์มแจ้งของหาย/พบของ
    public function create()
    {
        $categories = Category::all();
        $locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');

        return view('create', compact('categories', 'locations'));
    }

    // บันทึกข้อมูลโพสต์ลงฐานข้อมูล
    public function store(Request $request)
    {
        $validated = $request->validate([
            'postType'     => 'required|in:found,lost',
            'itemName'     => 'required|string|max:255',
            'category'     => 'required|exists:categories,id',
            'location'     => 'required|string|max:255',
            'date'         => 'required|date',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'reporterName' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:20',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = 'storage/' . $request->file('image')->store('items', 'public');
        }

        Item::create([
            'user_id'        => null, // โพสต์จากบุคคลทั่วไป ไม่ผูกกับหน่วยงาน
            'category_id'    => $validated['category'],
            'type'           => $validated['postType'],
            'title'          => $validated['itemName'],
            'description'    => $validated['description'] ?? null,
            'location'       => $validated['location'],
            'event_date'     => $validated['date'],
            'image_url'      => $imagePath,
            'status'         => $validated['postType'] === 'found' ? 'พบแล้ว' : 'หาย',
            'reporter_name'  => $validated['reporterName'] ?? null,
            'reporter_phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('posts.create')->with('success', 'บันทึกข้อมูลการแจ้งสำเร็จเรียบร้อยแล้ว');
    }
}
