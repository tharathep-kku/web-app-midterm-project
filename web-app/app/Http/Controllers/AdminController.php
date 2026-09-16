<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\FinderUser;

class AdminController extends Controller
{
    // หาข้อมูลแอดมินจากบัญชีที่ล็อกอินอยู่ (users.finder_user_id ชี้ไปที่ finder_users)
    private function currentAdmin()
    {
        $id = Auth::user()->finder_user_id;

        if ($id === null) {
            return null;
        }

        $user = FinderUser::find($id);

        if ($user === null || $user->role !== 'admin') {
            return null;
        }

        return $user;
    }

    // หน้าข้อมูลหลังบ้าน ดูโพสต์ทั้งหมดและกรองตามสถานะได้
    public function index(Request $request)
    {
        $admin = $this->currentAdmin();

        $approval_status = $request->input('approval_status') ?? '';
        $type = $request->input('type') ?? '';
        $keyword = trim($request->input('keyword') ?? '');

        $items = collect();

        if ($admin !== null) {
            $query = Item::query();

            if ($approval_status !== '') {
                $query->where('approval_status', $approval_status);
            }

            if ($type !== '') {
                $query->where('type', $type);
            }

            if ($keyword !== '') {
                $query->where('title', 'like', '%' . $keyword . '%');
            }

            $items = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();

            foreach ($items as $item) {
                $itemCategory = Category::find($item->category_id);
                $item->category_name = $itemCategory ? $itemCategory->name : 'อื่นๆ';

                $owner = FinderUser::find($item->user_id);
                $item->owner_name = $owner ? $owner->fullname : 'ไม่ทราบชื่อ';
                $item->owner_role = $owner ? $owner->role : '-';
            }
        }

        return view('admin.index', compact('admin', 'items', 'approval_status', 'type', 'keyword'));
    }

    public function approve(int $id)
    {
        $admin = $this->currentAdmin();

        if ($admin === null) {
            return redirect()->route('admin.index')->with('error', 'เฉพาะแอดมินเท่านั้นที่อนุมัติได้');
        }

        $item = Item::findOrFail($id);
        $item->approval_status = 'อนุมัติแล้ว';
        $item->reject_reason = null;
        $item->approved_by = $admin->id;
        $item->approved_at = now();
        $item->save();

        return redirect()->back()->with('success', 'อนุมัติโพสต์ ' . $item->title . ' แล้ว');
    }

    public function reject(Request $request, int $id)
    {
        $admin = $this->currentAdmin();

        if ($admin === null) {
            return redirect()->route('admin.index')->with('error', 'เฉพาะแอดมินเท่านั้นที่ปฏิเสธโพสต์ได้');
        }

        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'max:255'],
        ]);

        $item = Item::findOrFail($id);
        $item->approval_status = 'ไม่อนุมัติ';
        $item->reject_reason = $validated['reject_reason'];
        $item->approved_by = $admin->id;
        $item->approved_at = now();
        $item->save();

        return redirect()->back()->with('success', 'ปฏิเสธโพสต์ ' . $item->title . ' แล้ว');
    }

    // หน้าสถิติต่างๆ ของระบบ
    public function stats()
    {
        $admin = $this->currentAdmin();

        if ($admin === null) {
            return redirect()->route('admin.index')->with('error', 'เฉพาะแอดมินเท่านั้นที่ดูสถิติได้');
        }

        $total_item = Item::count();
        $wait_item = Item::where('approval_status', 'รออนุมัติ')->count();
        $pass_item = Item::where('approval_status', 'อนุมัติแล้ว')->count();
        $reject_item = Item::where('approval_status', 'ไม่อนุมัติ')->count();

        $found_item = Item::where('type', 'found')->count();
        $lost_item = Item::where('type', 'lost')->count();

        $returned_item = Item::where('status', 'ได้รับคืนแล้ว')->count();
        $waiting_owner = Item::where('status', 'ยังไม่พบเจ้าของ')->count();
        $waiting_confirm = Item::where('status', 'รอแอดมินยืนยัน')->count();

        // อัตราการได้รับคืน คิดเป็นเปอร์เซ็นต์ ต้องกันหารด้วยศูนย์ตอนที่ยังไม่มีข้อมูล
        $return_rate = 0;
        if ($total_item > 0) {
            $return_rate = ($returned_item / $total_item) * 100;
        }

        $first_date = Item::min('event_date');
        $last_date = Item::max('event_date');

        // จำนวนของที่แจ้งในแต่ละหมวดหมู่
        $categories = Category::all();
        $category_stats = [];
        foreach ($categories as $cat) {
            $category_stats[] = [
                'name' => $cat->name,
                'total' => Item::where('category_id', $cat->id)->count(),
                'returned' => Item::where('category_id', $cat->id)->where('status', 'ได้รับคืนแล้ว')->count(),
            ];
        }

        // จำนวนของที่แจ้งในแต่ละสถานที่
        $locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');
        $location_stats = [];
        foreach ($locations as $loc) {
            $location_stats[] = [
                'name' => $loc,
                'total' => Item::where('location', $loc)->count(),
            ];
        }

        // จำนวนโพสต์ของแต่ละหน่วยงาน
        $agencies = FinderUser::where('role', 'agency')->orderBy('id')->get();
        $agency_stats = [];
        foreach ($agencies as $ag) {
            $agency_stats[] = [
                'name' => $ag->fullname,
                'total' => Item::where('user_id', $ag->id)->count(),
                'wait' => Item::where('user_id', $ag->id)->where('approval_status', 'รออนุมัติ')->count(),
                'pass' => Item::where('user_id', $ag->id)->where('approval_status', 'อนุมัติแล้ว')->count(),
            ];
        }

        $total_user = FinderUser::count();
        $normal_user = FinderUser::where('role', 'user')->count();
        $agency_user = FinderUser::where('role', 'agency')->count();
        $admin_user = FinderUser::where('role', 'admin')->count();

        return view('admin.stats', compact(
            'admin',
            'total_item',
            'wait_item',
            'pass_item',
            'reject_item',
            'found_item',
            'lost_item',
            'returned_item',
            'waiting_owner',
            'waiting_confirm',
            'return_rate',
            'first_date',
            'last_date',
            'category_stats',
            'location_stats',
            'agency_stats',
            'total_user',
            'normal_user',
            'agency_user',
            'admin_user'
        ));
    }
}
