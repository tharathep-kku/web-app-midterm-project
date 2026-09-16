<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Item;
use App\Models\Category;

class ItemController extends Controller
{
    public function index(Request $request): View
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
