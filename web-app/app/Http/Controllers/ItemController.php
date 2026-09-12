<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\FinderUser;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // ถ้าฟอร์มยังไม่ถูก submit จะไม่มี key "searched" ติดมากับ query string เลย
        // (ใช้ hidden input ค่าคงที่ "1" แทนการเช็ค item_name เพราะถ้าค้นหาแบบเว้นทุกช่องว่าง
        // ค่าว่างจะถูกแปลงเป็น null แล้วหายไปจากลิงก์เปลี่ยนหน้า ทำให้ผลค้นหาหายไปตอนกด Next)
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

            $items = $query->orderBy('event_date', 'desc')->paginate(5)->withQueryString();

            foreach ($items as $item) {
                $itemCategory = Category::find($item->category_id);
                $item->category_name = $itemCategory ? $itemCategory->name : 'อื่นๆ';

                $reporter = FinderUser::find($item->user_id);
                $item->reporter_name = $reporter ? $reporter->fullname : 'ไม่ทราบชื่อ';
            }
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
    public function show(Item $item)
    {
        $item->category_name = optional(Category::find($item->category_id))->name ?? 'อื่นๆ';
        $item->reporter_name = optional(FinderUser::find($item->user_id))->fullname ?? 'ไม่ทราบชื่อ';
    
        return view('item', compact('item'));
    }
}
