<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\FinderUser;
use App\Models\User;

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

    // ดูโพสต์ทั้งหมดและกรองตามสถานะได้
    public function index(Request $request)
    {
        $admin = $this->currentAdmin();

        $type = $request->input('type') ?? '';
        $keyword = trim($request->input('keyword') ?? '');
        $status = $request->input('status') ?? '';
        $category = $request->input('category') ?? '';
        $location = trim($request->input('location') ?? '');
        $reporter = trim($request->input('reporter') ?? '');
        $start_date = $request->input('start_date') ?? '';
        $end_date = $request->input('end_date') ?? '';

        $items = collect();
        $handovers = collect();
        $users = collect();
        $categories = collect();
        $locations = collect();
        $statuses = collect();

        if ($admin !== null) {
            $handovers = Item::with('reporter')
            ->where('status', 'รอแอดมินยืนยัน')
            ->orderBy('id', 'DESC')
            ->get();

            // ponytail: ไม่แบ่งหน้า ถ้าผู้ใช้เยอะค่อยเปลี่ยนเป็น paginate()
            $users = User::with('finderUser')->orderBy('id')->get();

            $query = Item::query();
            $query = $this->filter($query, $request);

            // ตัวกรอง reporter เป็นสิทธิ์เฉพาะแอดมิน ไม่อยู่ใน filter() ที่ใช้ร่วมกับหน้าอื่น
            if ($reporter !== '') {
                $ownerIds = FinderUser::where('fullname', 'like', '%' . $reporter . '%')->pluck('id');
                $query->whereIn('user_id', $ownerIds);
            }

            $items = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();

            foreach ($items as $item) {
                $itemCategory = Category::find($item->category_id);
                $item->category_name = $itemCategory ? $itemCategory->name : 'อื่นๆ';

                $owner = FinderUser::find($item->user_id);
                $item->owner_name = $owner ? $owner->fullname : 'ไม่ทราบชื่อ';
                $item->owner_role = $owner ? $owner->role : '-';
            }

            $categories = Category::all();
            $locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');
            $statuses = Item::select('status')->distinct()->orderBy('status')->pluck('status');
        }

        return view('admin.index', compact(
            'admin',
            'items',
            'handovers',
            'users',
            'type',
            'keyword',
            'status',
            'category',
            'location',
            'reporter',
            'start_date',
            'end_date',
            'categories',
            'locations',
            'statuses'
        ));
    }

    // ใส่เงื่อนไข where ให้ query ของตารางโพสต์ทั้งหมด ตามช่องที่แอดมินกรอกจริง (ไม่ใช่ค่าว่าง)
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

    // ระงับบัญชี / ปลดแบน (กดซ้ำเพื่อสลับสถานะ) ระงับบัญชีแอดมินไม่ได้
    public function toggleBan(int $id)
    {
        $admin = $this->currentAdmin();

        if ($admin === null) {
            return redirect()->route('admin.index')->with('error', 'เฉพาะแอดมินเท่านั้นที่ระงับบัญชีได้');
        }

        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'ไม่สามารถระงับบัญชีแอดมินได้');
        }

        $user->is_banned = ! $user->is_banned;
        $user->save();

        return redirect()->back()->with('success', ($user->is_banned ? 'ระงับบัญชี ' : 'ปลดแบนบัญชี ') . $user->email . ' แล้ว');
    }

    // ลบโพสต์ใดก็ได้ออกจากระบบ
    public function destroy(int $id)
    {
        $admin = $this->currentAdmin();

        if ($admin === null) {
            return redirect()->route('admin.index')->with('error', 'เฉพาะแอดมินเท่านั้นที่ลบโพสต์ได้');
        }

        $item = Item::findOrFail($id);
        $item->deleteWithFiles();

        return redirect()->back()->with('success', 'ลบโพสต์ ' . $item->title . ' แล้ว');
    }

    // ยืนยันหลักฐานการส่งมอบ: จุดรับ-ส่งได้รับของแล้ว
    public function confirmHandover(int $id)
    {
        $admin = $this->currentAdmin();

        if ($admin === null) {
            return redirect()->route('admin.index')->with('error', 'เฉพาะแอดมินเท่านั้นที่ยืนยันการส่งมอบได้');
        }

        $item = Item::findOrFail($id);

        // ยืนยันได้เฉพาะรายการที่รอแอดมินยืนยันอยู่เท่านั้น
        if ($item->status !== 'รอแอดมินยืนยัน') {
            return redirect()->back()->with('error', 'รายการ ' . $item->title . ' ไม่ได้อยู่ในสถานะรอยืนยัน');
        }

        $item->status = 'ได้รับคืนแล้ว';
        $item->save();

        return redirect()->back()->with('success', 'ยืนยันการส่งมอบ ' . $item->title . ' แล้ว');
    }

    // ปฏิเสธหลักฐานการส่งมอบ: ให้ผู้ใช้ส่งหลักฐานใหม่
    public function rejectHandover(int $id)
    {
        $admin = $this->currentAdmin();

        if ($admin === null) {
            return redirect()->route('admin.index')->with('error', 'เฉพาะแอดมินเท่านั้นที่ปฏิเสธการส่งมอบได้');
        }

        $item = Item::findOrFail($id);

        if ($item->status !== 'รอแอดมินยืนยัน') {
            return redirect()->back()->with('error', 'รายการ ' . $item->title . ' ไม่ได้อยู่ในสถานะรอยืนยัน');
        }

        $item->status = 'หลักฐานไม่ถูกต้อง';
        $item->save();

        return redirect()->back()->with('success', 'ปฏิเสธหลักฐานการส่งมอบ ' . $item->title . ' แล้ว');
    }

    // หน้าสถิติต่างๆ ของระบบ
    public function stats()
    {
        $admin = $this->currentAdmin();

        if ($admin === null) {
            return redirect()->route('admin.index')->with('error', 'เฉพาะแอดมินเท่านั้นที่ดูสถิติได้');
        }

        $total_item = Item::count();

        $found_item = Item::where('type', 'found')->count();
        $lost_item = Item::where('type', 'lost')->count();

        $returned_item = Item::where('status', 'ได้รับคืนแล้ว')->count();
        $waiting_owner = Item::where('status', 'ยังไม่พบเจ้าของ')->count();
        $waiting_confirm = Item::where('status', 'รอแอดมินยืนยัน')->count();
        $invalid_evidence = Item::where('status', 'หลักฐานไม่ถูกต้อง')->count();

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

        $total_user = FinderUser::count();
        $normal_user = FinderUser::where('role', 'user')->count();
        $admin_user = FinderUser::where('role', 'admin')->count();

        return view('admin.stats', compact(
            'admin',
            'total_item',
            'found_item',
            'lost_item',
            'returned_item',
            'waiting_owner',
            'waiting_confirm',
            'invalid_evidence',
            'return_rate',
            'first_date',
            'last_date',
            'category_stats',
            'location_stats',
            'total_user',
            'normal_user',
            'admin_user'
        ));
    }
}
