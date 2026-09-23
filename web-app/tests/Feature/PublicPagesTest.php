<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;

// สร้างรายการของหาย/ของที่พบ 1 ชิ้น (ใส่เฉพาะ field ที่บังคับ ที่เหลือแก้ผ่าน $attrs)
function makeItem(array $attrs = []): Item
{
    // Category ไม่ได้ตั้ง $fillable จึงต้อง unguarded
    $category = Category::unguarded(fn () => Category::firstOrCreate(['name' => 'กุญแจ']));

    return Item::create($attrs + [
        'category_id' => $category->id,
        'type' => 'found',
        'title' => 'กุญแจรถ',
        'location' => 'หอสมุดกลาง',
        'event_date' => now()->subDays(2)->toDateString(),
        'status' => 'พบแล้ว',
    ]);
}

// ---------- หน้าแรก ----------

test('home lists items immediately without needing a search', function () {
    // ห้ามใช้ชื่อที่ซ้ำกับ placeholder ในฟอร์มค้นหา (เช่น "กระเป๋าตังค์สีน้ำตาล") ไม่งั้นเทสต์ผ่านเพราะเจอข้อความในฟอร์ม
    makeItem(['title' => 'พาวเวอร์แบงค์สีฟ้า']);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('พาวเวอร์แบงค์สีฟ้า');
});

test('home page has no search form', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('id="searchForm"', false);
});

test('home filters items by from date', function () {
    makeItem(['title' => 'ของเก่า', 'event_date' => '2026-01-01']);
    makeItem(['title' => 'ของใหม่', 'event_date' => '2026-06-01']);

    $response = $this->get(route('home', ['from' => '2026-05-01']));

    $response->assertSee('ของใหม่');
    $response->assertDontSee('ของเก่า');
});

test('home hides items returned to owner more than 6 months ago', function () {
    makeItem([
        'title' => 'คืนเจ้าของนานแล้ว',
        'status' => 'ได้รับคืนแล้ว',
        'returned_date' => now()->subMonths(7)->toDateString(),
    ]);
    makeItem([
        'title' => 'เพิ่งคืนเจ้าของ',
        'status' => 'ได้รับคืนแล้ว',
        'returned_date' => now()->subDays(10)->toDateString(),
    ]);

    $response = $this->get(route('home'));

    $response->assertSee('เพิ่งคืนเจ้าของ');
    $response->assertDontSee('คืนเจ้าของนานแล้ว');
});

// ---------- คลังประกาศ ----------

test('archive lists items returned to owner more than 6 months ago', function () {
    makeItem([
        'title' => 'คืนเจ้าของนานแล้ว',
        'status' => 'ได้รับคืนแล้ว',
        'returned_date' => now()->subMonths(7)->toDateString(),
    ]);
    makeItem(['title' => 'ยังไม่คืนเจ้าของ']);

    $response = $this->get(route('archive.home'));

    $response->assertOk();
    $response->assertSee('คืนเจ้าของนานแล้ว');
    $response->assertDontSee('ยังไม่คืนเจ้าของ');
});

// ---------- หน้าค้นหา ----------

test('search page shows the search form and no items until searched', function () {
    makeItem(['title' => 'กุญแจรถ']);

    $response = $this->get(route('search.home'));

    $response->assertOk();
    $response->assertSee('id="searchForm"', false);
    $response->assertDontSee('กุญแจรถ');
});

test('search returns only items matching the item name', function () {
    makeItem(['title' => 'กุญแจรถ']);
    makeItem(['title' => 'ร่มสีดำ']);

    $response = $this->get(route('search.home', ['searched' => 1, 'item_name' => 'กุญแจ']));

    $response->assertSee('กุญแจรถ');
    $response->assertDontSee('ร่มสีดำ');
});

// ---------- จุดรับ-ส่งคืน ----------

// หมายเหตุ: ตอนนี้ User ยังไม่ implements MustVerifyEmail จึงผ่านอยู่แล้ว (เทสต์นี้กันไว้เผื่อเปิดระบบยืนยันอีเมลภายหลัง)
test('users with an unverified email can open the return points page', function () {
    $this->actingAs(User::factory()->unverified()->create());

    $this->get(route('dashboard'))->assertOk();
});

// ---------- เมนู (แยกตาม role) ----------

test('guest menu shows login', function () {
    $this->get(route('home'))->assertSee('href="'.route('login').'"', false);
});

test('user menu shows report lost item', function () {
    $this->actingAs(User::factory()->create(['role' => 'user']));

    $this->get(route('home'))
        ->assertSee('href="'.route('posts.create').'"', false)
        ->assertDontSee('href="'.route('agency.index').'"', false)
        ->assertDontSee('href="'.route('admin.index').'"', false);
});

test('agency menu shows agency link', function () {
    $this->actingAs(User::factory()->create(['role' => 'agency']));

    $this->get(route('home'))
        ->assertSee('href="'.route('agency.index').'"', false)
        ->assertDontSee('href="'.route('posts.create').'"', false);
});

test('admin menu shows admin and stats links', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="'.route('admin.index').'"', false)
        ->assertSee('href="'.route('admin.stats').'"', false);
});
