# KKU Return: Search System — สรุปงานที่ทำ

สรุปการพัฒนา **ระบบค้นหา (Search System)** ของโปรเจกต์ "KKU Return: lost and found"
โดยแปลง prototype เดิม (`search.html` ที่เป็น JavaScript + localStorage ล้วน) ให้กลายเป็นระบบ
Full-stack จริงบน **Laravel 13** (Eloquent ORM + SQLite) ตามแนวทาง Migration → Model →
Seeder → Controller → Route → Blade View ที่สอนใน Lab08/Lab09

> หมายเหตุ: โปรเจกต์นี้เป็น Laravel skeleton เต็มรูปแบบอยู่แล้ว (มี Fortify, Livewire/Flux ติดตั้งไว้)
> จึงเลือกทำระบบค้นหาด้วยแนวทาง Laravel/Eloquent แทนแบบ Pure PHP + PDO ตรง ๆ

---

## 1. ภาพรวมสิ่งที่ทำ

- [x] สร้างตาราง `finder_users`, `categories`, `items` พร้อม Seed ข้อมูลตัวอย่าง
- [x] หน้า `/search` — ฟอร์มค้นหาแบบ sticky form + ตารางแสดงผลลัพธ์ (คงหน้าตาเดิมจาก `search.html`)
- [x] กรองผลลัพธ์ได้ตามชื่อสิ่งของ, หมวดหมู่, สถานที่ (พร้อม autocomplete), รายละเอียด, ช่วงวันที่
- [x] dropdown หมวดหมู่ และ autocomplete สถานที่ ดึงมาจากฐานข้อมูลจริง ไม่ใช่ค่าตายตัวในโค้ด
- [x] ซ่อนส่วน "ผลการค้นหา:" จนกว่าจะกดปุ่มค้นหา
- [x] ข้อความ "กำลังค้นหา..." ระหว่างรอโหลดหน้าถัดจากกดค้นหา
- [x] แบ่งหน้าผลลัพธ์ (Pagination) หน้าละ 5 รายการ พร้อมปุ่ม "« ก่อนหน้า" / "ถัดไป »" และเลขหน้า
- [x] มีรูปภาพประกอบครบทั้ง 14 รายการ

---

## 2. โครงสร้างฐานข้อมูล (SQLite ผ่าน Eloquent Migration)

ใช้ฐานข้อมูล `database/database.sqlite` (ตัวเดียวกับที่ Laravel ตั้งค่าไว้ใน `.env` อยู่แล้ว
`DB_CONNECTION=sqlite`) ไม่ได้สร้างไฟล์ฐานข้อมูลแยกต่างหาก

### ตาราง `finder_users`
เก็บบัญชีผู้ใช้ (คนแจ้งของหาย/ของที่พบ) — **ตั้งชื่อตารางว่า `finder_users` แทน `users`**
เพราะ Laravel มีตาราง `users` จริงสำหรับระบบ Login (Fortify) อยู่แล้วในโปรเจกต์นี้
ถ้าใช้ชื่อซ้ำจะไปชนกับระบบ auth เดิม

| คอลัมน์ | ชนิด | รายละเอียด |
|---|---|---|
| id | bigint, PK | auto increment |
| username | string | ชื่อผู้ใช้ |
| fullname | string | ชื่อ-นามสกุลจริง (ภาษาไทย) — ใช้แสดงในคอลัมน์ "ชื่อผู้ใช้" ของตารางผลค้นหา |
| email | string | อีเมล @kku.ac.th หรือ @kkumail.com |
| phone | string | เบอร์โทร |
| role | string | `user` หรือ `admin` |
| created_at / updated_at | timestamp | Laravel สร้างให้อัตโนมัติ |

**ข้อมูลตัวอย่าง:** 10 คน (ไฟล์ `database/seeders/FinderUserSeeder.php`) — user ปกติ 9 คน,
admin 1 คน (พิมพ์ชนก รุ่งเรือง)

### ตาราง `categories`
| คอลัมน์ | ชนิด |
|---|---|
| id | bigint, PK |
| name | string |

**ข้อมูลตัวอย่าง:** 6 หมวดหมู่ (ไฟล์ `database/seeders/CategorySeeder.php`) — กุญแจ, กระเป๋าตังค์,
เครื่องประดับ, เอกสาร, อุปกรณ์อิเล็กทรอนิกส์, อื่นๆ

### ตาราง `items`
| คอลัมน์ | ชนิด | รายละเอียด |
|---|---|---|
| id | bigint, PK | |
| user_id | unsigned bigint | อ้างอิง `finder_users.id` (**ไม่ได้ตั้ง foreign key constraint** ในระดับ DB เพื่อให้เรียบง่ายตามระดับบทเรียน — ใน Controller ใช้ `FinderUser::find($item->user_id)` หา record เอาเอง แทนการทำ Eloquent relationship) |
| category_id | unsigned bigint | อ้างอิง `categories.id` เหมือนกัน ไม่มี constraint เช่นกัน |
| type | string | `found` (พบของ) หรือ `lost` (ทำของหาย) |
| title | string | ชื่อสิ่งของ — ใช้ค้นหาแบบ `LIKE` |
| description | text, nullable | รายละเอียดเพิ่มเติม — ใช้ค้นหาแบบ `LIKE` เช่นกัน |
| location | string | สถานที่พบ/หาย — ใช้ค้นหาแบบ `LIKE` และเป็นแหล่งข้อมูลของ `<datalist>` autocomplete |
| event_date | date (`YYYY-MM-DD`) | วันที่พบ/หาย — ใช้กรองแบบช่วงวันที่ (`>=` / `<=`) |
| image_url | string, nullable | path รูปภาพ relative จาก `public/` เช่น `images/items/carkey.png` — แสดงผ่าน `asset($item->image_url)` |
| status | string | `ยังไม่พบเจ้าของ`, `ได้รับคืนแล้ว`, `รอแอดมินยืนยัน` |

**ข้อมูลตัวอย่างปัจจุบัน:** 14 รายการ (ไฟล์ `database/seeders/ItemSeeder.php`) กระจายอยู่ 5 สถานที่
(อาคารวิทยวิภาส, อาคารพจน์ สารสิน, ศูนย์อาหารคอมเพล็กซ์, ศูนย์ประชุมกาญจนาภิเษก, อาคาร SC09)
ผูกกับ `user_id` ครบทั้ง 10 คน และ**มีรูปภาพครบทุกรายการ** (ไฟล์อยู่ใน `public/images/items/`):

| # | title | image_url | location |
|---|---|---|---|
| 1 | กุญแจรถเก๋งสีดำ | carkey.png | อาคารวิทยวิภาส |
| 2 | กระเป๋าตังค์สีน้ำตาล | money.png | อาคารพจน์ สารสิน |
| 3 | กำไลข้อมือ | ring.png | ศูนย์ประชุมกาญจนาภิเษก |
| 4 | บัตรประจำตัวนักศึกษา | studentID.png | ศูนย์อาหารคอมเพล็กซ์ |
| 5 | หูฟังไร้สายสีขาว | earphone.png | อาคาร SC09 |
| 6 | ร่มพับสีดำ | umbella_black.png | อาคารพจน์ สารสิน |
| 7 | กุญแจหอพัก | dorm_key.png | ศูนย์อาหารคอมเพล็กซ์ |
| 8 | กระเป๋าสตางค์ใบเล็กสีดำ | wallet_black.png | ศูนย์ประชุมกาญจนาภิเษก |
| 9 | ต่างหูมุก | earring.png | อาคารวิทยวิภาส |
| 10 | สมุดโน้ตปกสีฟ้า | notebook_blue.png | อาคารพจน์ สารสิน |
| 11 | พาวเวอร์แบงค์สีดำ | powerbank_black.png | ศูนย์อาหารคอมเพล็กซ์ |
| 12 | แว่นตากันแดด | sunglasses_black.png | ศูนย์ประชุมกาญจนาภิเษก |
| 13 | กุญแจรถจักรยานยนต์ | motorcycle_key.png | อาคารวิทยวิภาส |
| 14 | กระเป๋าเงินใบยาวสีแดง | wallet_red.png | อาคารพจน์ สารสิน |

**ไม่ได้ใช้ Eloquent Relationship (`belongsTo`)** — ตั้งใจให้เรียบง่ายตามระดับบทเรียน โดยใน
Controller จะ loop รายการที่ได้แล้ว `Category::find()` / `FinderUser::find()` เอาเองทีละรายการ
เพื่อเติมชื่อหมวดหมู่/ชื่อผู้แจ้งเข้าไปใน object ก่อนส่งไปหน้า view

---

## 3. ไฟล์ใหม่ทั้งหมด — ทำหน้าที่อะไรบ้าง

### Migrations (สร้างโครงตาราง — แก้ schema เท่านั้น ไม่เกี่ยวกับข้อมูล)
| ไฟล์ | หน้าที่ |
|---|---|
| `database/migrations/2026_09_07_100001_create_finder_users_table.php` | สร้างตาราง `finder_users` |
| `database/migrations/2026_09_07_100002_create_categories_table.php` | สร้างตาราง `categories` |
| `database/migrations/2026_09_07_100003_create_items_table.php` | สร้างตาราง `items` |

### Models (Eloquent — คลาสเปล่า ให้ Laravel ผูกกับตารางอัตโนมัติจากชื่อคลาส)
| ไฟล์ | ผูกกับตาราง |
|---|---|
| `app/Models/FinderUser.php` | `finder_users` (Laravel เดาชื่อตารางจากชื่อคลาสอัตโนมัติ: `FinderUser` → `finder_users`) |
| `app/Models/Category.php` | `categories` |
| `app/Models/Item.php` | `items` |

ทั้ง 3 ไฟล์เป็นคลาสเปล่า (`class Item extends Model {}`) ไม่มีการเขียน relationship
(`belongsTo`/`hasMany`) เพื่อให้อยู่ในระดับความยากเดียวกับที่สอนใน Lab

### Seeders (ใส่ข้อมูลตัวอย่าง — ใช้ `DB::table(...)->insert([...])` เหมือน MovieSeeder/AIModelSeeder)
| ไฟล์ | หน้าที่ |
|---|---|
| `database/seeders/FinderUserSeeder.php` | Insert ผู้ใช้ 10 คน |
| `database/seeders/CategorySeeder.php` | Insert หมวดหมู่ 6 รายการ |
| `database/seeders/ItemSeeder.php` | Insert สิ่งของ 14 รายการ (มีรูปภาพครบทุกรายการ) |
| `database/seeders/DatabaseSeeder.php` *(แก้ไขไฟล์เดิม)* | เพิ่ม `$this->call(...)` เรียก 3 Seeder ด้านบน |

**ข้อควรระวัง:** Seeder ทั้ง 3 ตัวนี้ใช้ `insert()` ซึ่ง**เพิ่มแถวใหม่เสมอ** ไม่ใช่ update — ถ้าแก้ข้อมูล
ในไฟล์ seeder แล้วรันซ้ำตรง ๆ จะได้ข้อมูลซ้ำ ต้อง `truncate()` ตารางก่อนเสมอ (ดูหัวข้อ 6)

### Controller
| ไฟล์ | หน้าที่ |
|---|---|
| `app/Http/Controllers/ItemController.php` | Method `index()` — อ่านค่าค้นหาจาก query string, กรองข้อมูลด้วย Eloquent query builder, แบ่งหน้าด้วย `paginate(5)`, เติมชื่อหมวดหมู่/ชื่อผู้แจ้ง และรายชื่อสถานที่ทั้งหมดให้แต่ละรายการ แล้วส่งไปแสดงที่ view `search` |

### Route
| ไฟล์ | หน้าที่ |
|---|---|
| `routes/web.php` *(แก้ไขไฟล์เดิม)* | เพิ่ม `Route::get('/search', [ItemController::class, 'index'])->name('search.index');` |

### Views (Blade)
| ไฟล์ | หน้าที่ |
|---|---|
| `resources/views/search.blade.php` | หน้าเว็บค้นหา — คง Navbar/Footer/ข้อความจาก `search.html` เดิม, ฟอร์ม sticky, dropdown หมวดหมู่ + datalist สถานที่ดึงจาก DB, ตารางผลลัพธ์, ข้อความ "กำลังค้นหา...", ซ่อนผลลัพธ์จนกว่าจะกดค้นหา |
| `resources/views/partials/pagination.blade.php` | ปุ่มแบ่งหน้า (ก่อนหน้า/ถัดไป/เลขหน้า) แบบ HTML ธรรมดา เขียนขึ้นเองแทนของ Laravel default เพราะของเดิมออกแบบมาสำหรับเว็บที่ใช้ Tailwind CSS ซึ่งหน้านี้ไม่ได้โหลด Tailwind |

### ไฟล์ static เพิ่มเติม
| ไฟล์ | หน้าที่ |
|---|---|
| `public/images/items/*.png` (14 ไฟล์) | รูปภาพประกอบของแต่ละรายการใน `items` — ดูตารางจับคู่ในหัวข้อ 2 |

---

## 4. ตัวแปรสำคัญ (Important Variables)

### ใน `ItemController@index`
| ตัวแปร | มาจากไหน | ความสำคัญ |
|---|---|---|
| `$searched` | `$request->has('searched')` | **ตัวสวิตช์หลัก** ว่าหน้านี้จะ query ฐานข้อมูล + แสดงผลลัพธ์หรือไม่ ถ้า `false` จะข้าม logic ค้นหาทั้งหมด และ Blade จะไม่แสดงส่วน "ผลการค้นหา:" เลย มาจาก hidden input `<input type="hidden" name="searched" value="1">` ในฟอร์ม (ไม่ใช้ `item_name` เพราะค่าว่างจะหายไปจาก URL ตอนกดเปลี่ยนหน้า — ดูบั๊ก 2 ในหัวข้อ 5) |
| `$item_name`, `$category`, `$location`, `$description`, `$start_date`, `$end_date` | `$request->input('key') ?? ''` | ค่าที่ผู้ใช้กรอกในฟอร์ม ใช้ 2 ต่อ: (1) สร้างเงื่อนไข `where(...)` และ (2) ส่งกลับไปแสดงใน `value="{{ ... }}"` ของฟอร์มเพื่อทำ sticky form ต้องใช้ `?? ''` ไม่ใช่ default ตัวที่สองของ `input()` เพราะ Laravel แปลงค่าว่างเป็น `null` เอง |
| `$items` | `Item::query()->...->paginate(5)->withQueryString()` | ผลลัพธ์การค้นหาของ**หน้าปัจจุบัน**เท่านั้น (ไม่ใช่ทั้งหมด) เป็น `LengthAwarePaginator` ไม่ใช่ Collection ธรรมดา — ใช้ `->links()` สร้างปุ่มแบ่งหน้าได้ในตัว |
| `$categories` | `Category::all()` | ใช้สร้าง `<option>` ใน dropdown หมวดหมู่ |
| `$locations` | `Item::select('location')->distinct()->orderBy('location')->pluck('location')` | ใช้สร้าง `<option>` ใน `<datalist>` autocomplete สถานที่ — ดึงจากสถานที่ที่ **มีอยู่จริงในตาราง items** ดังนั้นเพิ่ม location ใหม่ใน seeder แล้ว reseed จะขึ้น autocomplete เองอัตโนมัติ ไม่ต้องแก้ Blade |
| `$item->category_name` / `$item->reporter_name` | เติมเข้าไปใน loop หลัง query (ไม่ได้มาจากตาราง `items` โดยตรง) | ชื่อหมวดหมู่/ชื่อผู้แจ้งจริง ๆ ที่ map มาจาก `category_id`/`user_id` เพื่อโชว์ในตาราง เพราะไม่ได้ใช้ Eloquent relationship |

### ใน `search.blade.php`
| ตัวแปร/element | ความสำคัญ |
|---|---|
| `<input type="hidden" name="searched" value="1">` | ตัวบอก Controller ว่า "ฟอร์มนี้ถูก submit แล้ว" ค่าคงที่ `"1"` ไม่มีวันกลายเป็นค่าว่าง จึงไม่หายไปตอนกดปุ่มแบ่งหน้า |
| `#loadingMessage` (ซ่อนด้วย `display:none`) | โชว์ข้อความ "กำลังค้นหา..." ผ่าน `onsubmit` ของฟอร์ม (vanilla JS ธรรมดา ไม่ใช้ AJAX) |
| `@if ($searched) ... @endif` | ครอบส่วนตารางผลลัพธ์ทั้งหมด — ถ้ายังไม่ค้นหาจะไม่ render ส่วนนี้เลย |
| `$items->links('partials.pagination')` | เรียกใช้ไฟล์ pagination ที่เขียนเอง แทนของ default ของ Laravel |

---

## 5. Logic การค้นหา (ใน `ItemController@index`)

1. เช็คว่ากดปุ่มค้นหามาแล้วหรือยัง จาก `$searched = $request->has('searched')`
2. อ่านค่าจากฟอร์มทีละช่อง แล้ว `trim()` เพื่อตัดช่องว่างหน้า-หลัง
3. ต่อเงื่อนไข `where(...)` เข้ากับ query ทีละเงื่อนไข เฉพาะช่องที่ผู้ใช้กรอกจริง (ไม่ใช่ค่าว่าง):
   - `item_name` → `title LIKE %...%`
   - `category` → `category_id = ...`
   - `location` → `location LIKE %...%`
   - `description` → `description LIKE %...%`
   - `start_date` / `end_date` → `event_date >= / <=`
4. เรียงผลลัพธ์ตามวันที่ล่าสุดก่อน (`orderBy('event_date', 'desc')`)
5. แบ่งหน้าด้วย `paginate(5)` แล้วต่อ query string เดิมเข้ากับลิงก์เปลี่ยนหน้าด้วย `withQueryString()`
6. Loop ผลลัพธ์แต่ละหน้าเพื่อหาไป "ชื่อหมวดหมู่" และ "ชื่อผู้แจ้ง" มาแปะเพิ่มใน object ก่อนส่งไป view
7. ดึงรายชื่อสถานที่ทั้งหมด (distinct) จากตาราง `items` เพื่อใช้เป็น autocomplete

---

## 6. บั๊กที่เจอระหว่างทำ และวิธีแก้ (บันทึกไว้กันลืม)

### บั๊ก 1: ค้นหาช่วงวันที่แล้วเจอ 500 Internal Server Error
**สาเหตุ:** Laravel มี middleware ชื่อ `ConvertEmptyStringsToNull` ที่แปลงค่าฟอร์มที่เป็น
ค่าว่าง (`""`) ให้กลายเป็น `null` โดยอัตโนมัติทุก request เมื่อโค้ดเดิมเขียน
`$request->input('start_date', '')` ค่า default `''` จะทำงานเฉพาะตอนไม่มี key นี้เลย
แต่พอ key มีอยู่แต่ค่าเป็น `null` (เพราะถูกแปลงมา) โค้ดจะได้ `null` ไม่ใช่ `''` แล้วเอา `null`
ไปเทียบกับ operator `>=` ใน query builder ทำให้เกิด `InvalidArgumentException:
Illegal operator and value combination`

**วิธีแก้:** เปลี่ยนจาก `$request->input('key', '')` เป็น `$request->input('key') ?? ''`
ทุกช่อง เพื่อดักทั้งกรณี "ไม่มี key" และกรณี "มี key แต่ค่าเป็น null" ให้กลายเป็น `''` เหมือนกัน

### บั๊ก 2: กดปุ่ม "Next »" แล้วผลการค้นหาหายไปทั้งหมด
**สาเหตุ:** ตอนแรกเช็คว่า "ค้นหาแล้วหรือยัง" จากการมี key `item_name` อยู่ใน query string
แต่ถ้าค้นหาแบบเว้นว่างทุกช่อง (ดูของทั้งหมด) ทุกค่าจะเป็น `null` (จากมิดเดิลแวร์ในบั๊ก 1)
และฟังก์ชัน `withQueryString()` ของ Laravel (ใช้ `http_build_query()` ภายใน) **จะตัด key
ที่ค่าเป็น null ทิ้งจาก URL โดยอัตโนมัติ** ทำให้ลิงก์ "Next »" กลายเป็นแค่ `/search?page=2`
ไม่มี `item_name` ติดไปด้วย ระบบเลยเข้าใจผิดว่า "ยังไม่ได้ค้นหา" แล้วซ่อนผลลัพธ์ทั้งหมด

**วิธีแก้:** เพิ่ม hidden input `name="searched" value="1"` ที่มีค่าคงที่ไม่มีวันเป็นค่าว่าง
แล้วเปลี่ยนมาเช็คจาก key `searched` แทน `item_name` — วิธีนี้ค่าจะไม่มีวันหายไปตอนกดเปลี่ยนหน้า

### เรื่องรอง 1: ปุ่ม Previous/Next แสดงเป็นภาษาอังกฤษดิบ ๆ ไม่มีสไตล์
**สาเหตุ:** `{{ $items->links() }}` เรียกใช้ Blade view เริ่มต้นของ Laravel
(`vendor/laravel/framework/.../pagination/tailwind.blade.php`) ซึ่งออกแบบมาสำหรับเว็บที่ใช้
Tailwind CSS แต่หน้า `search.blade.php` ไม่ได้โหลด Tailwind เลย ทำให้ class ต่าง ๆ
(`sm:hidden`, `sm:flex` ฯลฯ) ไม่มีผลอะไร กลายเป็น markup รกเกินความจำเป็น

**วิธีแก้:** เขียนไฟล์ pagination เอง (`resources/views/partials/pagination.blade.php`)
เป็น HTML ธรรมดาไม่มี class ซับซ้อน ข้อความเป็นภาษาไทย แล้วเรียกใช้ผ่าน
`$items->links('partials.pagination')` แทนของ default

### เรื่องรอง 2: เพิ่ม location ใหม่ ("อาคาร SC09") ใน seeder แล้วไม่ขึ้น autocomplete
**สาเหตุ:** `<datalist>` เดิม hardcode รายชื่อสถานที่ไว้ตายตัว 4 ค่าใน Blade โดยตรง
ไม่ได้ดึงจากฐานข้อมูล ต่างจาก dropdown หมวดหมู่ที่ดึงจาก DB อยู่แล้ว

**วิธีแก้:** เพิ่ม `$locations = Item::select('location')->distinct()->orderBy('location')->pluck('location');`
ใน Controller แล้วเปลี่ยน `<datalist>` ให้ `@foreach ($locations as $loc)` แทนการ hardcode
ต่อจากนี้เพิ่ม/แก้ location ใน seeder แล้ว reseed ก็จะขึ้น autocomplete เองอัตโนมัติ

---

## 7. วิธีรันโปรเจกต์ (ทดสอบผ่านแล้ว)

### รันครั้งแรก
```bash
composer install
copy .env.example .env          # ถ้ายังไม่มี .env
php artisan key:generate
php artisan migrate             # สร้างตารางทั้งหมด รวมถึง finder_users / categories / items
php artisan db:seed             # ใส่ข้อมูลตัวอย่างทั้งหมด (User, Movie... + FinderUser, Category, Item)
php artisan serve               # แล้วเข้า http://127.0.0.1:8000/search
```

### แก้ข้อมูลใน Seeder แล้วอยากอัปเดตฐานข้อมูล (ไม่ต้อง migrate ใหม่ — migration แก้แค่โครงตาราง)
เนื่องจาก seeder ใช้ `insert()` การรันซ้ำตรง ๆ จะได้ข้อมูลซ้ำ ต้องล้างตารางก่อนเสมอ:

```bash
# ล้างเฉพาะตาราง items แล้ว seed ใหม่ (แนะนำ ไม่กระทบตารางอื่น)
php artisan tinker --execute="DB::table('items')->truncate(); (new \Database\Seeders\ItemSeeder)->run();"

# หรือรีเซ็ตทั้งฐานข้อมูลใหม่หมด (ลบทุกตาราง สร้าง+seed ใหม่ทั้งหมด)
# ระวัง: ลบข้อมูลทุกตารางรวมถึง users จริงของระบบ login ด้วย
php artisan migrate:fresh --seed
```

ถ้าแก้โครงสร้างตาราง (เพิ่ม/ลบคอลัมน์) เท่านั้นถึงจะต้องเขียน migration ใหม่จริง ๆ

ทดสอบจริงแล้วทุกเคส: ค้นหาแบบเว้นว่างทุกช่อง, ค้นหาตามชื่อ, ค้นหาตามหมวดหมู่, ค้นหาตามช่วงวันที่,
ค้นหาไม่เจอ (ขึ้นข้อความแจ้ง), การแบ่งหน้า (5 รายการ/หน้า, กด Next/Previous ข้ามหน้าโดยค่าค้นหา
เดิมไม่หาย), autocomplete สถานที่ที่เพิ่มใหม่ขึ้นถูกต้อง

---

## 8. สิ่งที่ยังไม่ทำ (นอกขอบเขตของ "ระบบค้นหา")

- หน้ารายละเอียดสิ่งของ (ปุ่ม "More" ชี้ไปที่ `/items/{id}` แต่ยังไม่มี route/controller/view รองรับ)
- ระบบ Login/Register จริง (หน้า `login.html`, `register.html`, `create.html`, `profile.html`
  ยังเป็นไฟล์ static เดิม ไม่ได้แปลงเป็น Laravel)
- User ประเภท "หน่วยงาน/องค์กร" (Institution) ที่รับฝากของ/ยืนยันหลักฐาน — เป็นแนวคิดที่คุยกันไว้
  แต่ยังไม่ได้สร้าง เพราะเป็นคนละฟีเจอร์กับระบบค้นหา (ดูหัวข้อ "แนวทางถ้าจะทำในอนาคต" ด้านล่าง)

### แนวทางถ้าจะทำ Institution user ในอนาคต
มีสองทางเลือก:
1. เพิ่มค่า `role` เป็น `'institution'` ในตาราง `finder_users` เดิม — ง่าย ไม่ต้องเพิ่มตาราง
2. แยกตาราง `institutions` ใหม่ (มี field เช่น ที่ตั้ง, เวลาเปิด-ปิด) — เหมาะถ้าต้องมีข้อมูล
   เฉพาะที่ user ทั่วไปไม่มี แต่จะซับซ้อนขึ้นอีกระดับ

# หน้าแรก (Home) และคลังประกาศ (Archive)

แปลงโค้ด `home.php` เดิม (PHP + mysqli + `style.css`) ให้เป็น Laravel แบบเดียวกับระบบค้นหา
หน้าที่ทำใหม่ **ไม่มี CSS** ใช้ HTML ธรรมดาเหมือน `search.blade.php`

---

## 1. สิ่งที่ทำ

- [x] หน้า `/` — ตารางประกาศ แบ่งหน้าละ 5 รายการ
- [x] แถบสรุปตัวเลข: ประกาศทั้งหมด / ยังไม่พบเจ้าของ / ได้รับคืนแล้ว / รอแอดมินยืนยัน
- [x] ช่องค้นหาเร็ว 1 ช่อง ส่งต่อไปหน้า `/search` พร้อมผลลัพธ์
- [x] กรองตามประเภท: ทั้งหมด · พบของ · ของหาย
- [x] ปุ่มลัดช่วงเวลา: ทั้งหมด · 7 วันล่าสุด · 30 วันล่าสุด · 3 เดือนล่าสุด
- [x] เรียงลำดับ: ใหม่สุด · เก่าสุด (ตัวกรองอื่นไม่หายตอนกด)
- [x] แสดง "พบ N รายการ ระหว่าง dd/mm/yyyy ถึง dd/mm/yyyy"
- [x] ซ่อนประกาศที่ได้รับคืนแล้วเกิน 7 วันออกจากหน้าแรก (ไม่ลบข้อมูลจริง)
- [x] นับถอยหลัง "เก็บเข้าคลังในอีก N วัน" ใต้สถานะของที่คืนแล้วแต่ยังไม่ครบกำหนด
- [x] หน้า `/archive` — แสดงเฉพาะประกาศที่คืนแล้วเกิน 7 วัน
- [x] เพิ่มคอลัมน์ `returned_date` ในตาราง `items`
- [x] เมนูกลาง `partials/menu.blade.php` ใช้ร่วมในหน้า home, search, archive

---

## 2. ฐานข้อมูล

### เพิ่มคอลัมน์ในตาราง `items`
| คอลัมน์ | ชนิด | รายละเอียด |
|---|---|---|
| returned_date | date, nullable | วันที่คืนของให้เจ้าของ ใช้คู่กับ `status = 'ได้รับคืนแล้ว'` |

เพิ่มผ่าน migration ใหม่ ไม่ได้แก้ migration เดิม

### ข้อมูลตัวอย่าง (`ItemSeeder.php`)

ใช้วันที่แบบ **นับจากวันที่ seed** ไม่ใช่วันที่ตายตัว เพื่อให้ข้อมูลไม่หมดอายุ
(ถ้าเขียน `'2026-02-10'` ตายตัว พอเวลาผ่านไปของจะเข้าคลังหมด ตัวนับถอยหลังจะไม่มีวันแสดง)

```php
'event_date'    => date('Y-m-d', strtotime('-10 days')),
'returned_date' => date('Y-m-d', strtotime('-5 days')),
```

| id | title | เจอเมื่อ | คืนเมื่อ | แสดงที่ |
|---|---|---|---|---|
| 2 | กระเป๋าตังค์สีน้ำตาล | -25 วัน | -20 วัน | หน้าคลัง |
| 3 | กำไลข้อมือ | -10 วัน | -5 วัน | หน้าแรก + "อีก 2 วัน" |
| 12 | แว่นตากันแดด | -7 วัน | -1 วัน | หน้าแรก + "อีก 6 วัน" |

รายการอื่น `returned_date` เป็น `NULL` — ครบทั้ง 3 สถานะให้เห็นในครั้งเดียว

---

## 3. ไฟล์

### ไฟล์ใหม่
| ไฟล์ | หน้าที่ |
|---|---|
| `database/migrations/2026_09_12_000001_add_returned_date_to_items_table.php` | เพิ่มคอลัมน์ `returned_date` |
| `resources/views/home.blade.php` | หน้าแรก |
| `resources/views/archive.blade.php` | หน้าคลังประกาศของคืน |
| `resources/views/partials/menu.blade.php` | เมนูกลาง 8 ลิงก์ |

### ไฟล์ที่แก้
| ไฟล์ | แก้อะไร |
|---|---|
| `routes/web.php` | `/` เปลี่ยนจาก `Route::view('/', 'welcome')` เป็น `ItemController@home` และเพิ่ม `/archive` |
| `app/Http/Controllers/ItemController.php` | เพิ่ม `home()` และ `archive()` (ไม่แตะ `index()`) |
| `database/seeders/ItemSeeder.php` | ใส่ `returned_date` 3 รายการ (วันที่สัมพัทธ์) + loop เติม `null` ให้รายการที่เหลือ |
| `resources/views/search.blade.php` | เปลี่ยนเมนูเดิมเป็น `@include('partials.menu')` |

```php
Route::get('/', [ItemController::class, 'home'])->name('home');
Route::get('/archive', [ItemController::class, 'archive'])->name('archive.home');
```

---

## 4. Logic

### ค่าคงที่ `ARCHIVE_DAYS`
จำนวนวันก่อนย้ายเข้าคลัง **เก็บไว้ที่เดียว** บนสุดของคลาส
```php
private const ARCHIVE_DAYS = 7;
```
ถูกใช้ 3 ที่: `home()` (ซ่อนของเก่า), `archive()` (ดึงของเก่า), `addNames()` (นับถอยหลัง)
เดิมพิมพ์ `-6 months` ซ้ำ 2 ที่ ถ้าแก้ไม่ครบจะมีของหายไปจากทั้งสองหน้า หรือโผล่ทั้งสองหน้า
ตอนนี้เปลี่ยนเลขที่เดียว ทุกที่เปลี่ยนตามพร้อมกัน

### หน้าแรก (`ItemController@home`)
1. อ่าน `from`, `to`, `type`, `sort` จาก query string ด้วย `$request->input('...') ?? ''`
2. คำนวณเส้นแบ่ง `date('Y-m-d', strtotime('-' . self::ARCHIVE_DAYS . ' days'))`
3. แสดงรายการที่เข้าเงื่อนไขข้อใดข้อหนึ่ง (ห่อด้วย closure ให้เป็นวงเล็บเดียวกัน):
   ```sql
   WHERE (status != 'ได้รับคืนแล้ว'
          OR returned_date IS NULL
          OR returned_date > เส้นแบ่ง)
   ```
4. ถ้ากรอกวันที่ → เพิ่ม `event_date >= $from` / `event_date <= $to`
5. ถ้าเลือกประเภท → เพิ่ม `where('type', $type)` (`found` / `lost`)
6. เรียงด้วย `orderBy('event_date', $direction)` แล้ว `paginate(5)->withQueryString()`
7. `addNames($items)` เติมชื่อหมวดหมู่ ชื่อผู้แจ้ง และจำนวนวันที่เหลือก่อนเข้าคลัง
8. นับตัวเลข 4 ตัวด้วย `Item::count()` / `Item::where('status', ...)->count()`

### เรียงลำดับ — ป้องกัน SQL injection
```php
$direction = $sort === 'old' ? 'asc' : 'desc';
```
**ห้ามส่งค่าจาก URL เข้า `orderBy()` ตรง ๆ** เพราะทิศทางการเรียงถูกต่อเข้าคำสั่ง SQL ตรง ๆ
ไม่ได้ผ่านระบบ binding เหมือนค่าใน `where()` — จึงใช้ค่าจากผู้ใช้มา**เลือก**ค่าที่เราเขียนไว้เองแทน (whitelist)
พิมพ์อะไรแปลก ๆ มาก็ได้ `desc` เสมอ

### ตัวนับถอยหลัง (`addNames()`)
```php
$item->days_left = 0;
if ($item->status === 'ได้รับคืนแล้ว' && !empty($item->returned_date)) {
    $archiveTime = strtotime($item->returned_date . ' +' . self::ARCHIVE_DAYS . ' days');
    $item->days_left = ceil(($archiveTime - time()) / 86400);
}
```
- `strtotime()` แปลงวันที่เป็นวินาที ลบกับ `time()` แล้วหาร `86400` (วินาทีใน 1 วัน) ได้จำนวนวัน
- `ceil()` ปัดขึ้น เพราะเหลือ 3.4 วันต้องบอก "อีก 4 วัน" ไม่ใช่ "อีก 3 วัน"
- คำนวณใน controller ไม่ใช่ใน view เพราะ view มีหน้าที่แสดงผลอย่างเดียว

### หน้าคลัง (`ItemController@archive`)
ตรงข้ามกับหน้าแรก ต้องเข้าครบทุกข้อ:
```sql
WHERE status = 'ได้รับคืนแล้ว'
  AND returned_date IS NOT NULL
  AND returned_date <= เส้นแบ่ง
ORDER BY returned_date DESC
```
ประกาศแต่ละรายการจะอยู่หน้าแรก **หรือ** หน้าคลัง ที่ใดที่หนึ่งเท่านั้น เพราะทั้งสองหน้าใช้ `ARCHIVE_DAYS` ตัวเดียวกัน

### ตัวแปรสำคัญ
| ตัวแปร | ความสำคัญ |
|---|---|
| `$from`, `$to` | ช่วงวันที่ ใช้กรอง และแสดงในข้อความ "ระหว่าง ... ถึง ..." |
| `$type` | `found` / `lost` / ว่าง = ทั้งหมด |
| `$sort` | `old` = เรียงเก่าก่อน นอกนั้น = ใหม่ก่อน |
| `$direction` | ค่าจริงที่ส่งเข้า `orderBy()` มีได้แค่ `asc` หรือ `desc` |
| `$items` | `LengthAwarePaginator` หน้าละ 5 — นับทั้งหมดด้วย `total()` ไม่ใช่ `count()` |
| `$item->days_left` | จำนวนวันก่อนเข้าคลัง (0 = ไม่ต้องแสดง) ไม่ใช่คอลัมน์ในฐานข้อมูล |

---

## 5. บั๊กที่เจอและวิธีแก้

### บั๊ก 1: `migrate:fresh --seed` พัง `all VALUES must have the same number of terms`
**สาเหตุ:** `DB::table('items')->insert([...])` แบบหลายแถวพร้อมกัน บังคับให้ทุกแถวมีคีย์ชุดเดียวกัน
แต่ใส่ `returned_date` แค่ 3 แถว

**วิธีแก้:** เก็บข้อมูลไว้ใน `$items` ก่อน แล้ว loop เติม `returned_date => null` ให้แถวที่ไม่มี จากนั้นค่อย insert

> บั๊กนี้กลับมาอีกครั้งหลัง merge เพราะมีคนเขียน loop ใหม่ (เพิ่ม `return_unit_id`) ทับบรรทัดเติม `null`
> ถ้าเจอ error นี้อีก ให้ดูว่า loop ท้าย `ItemSeeder` ยังมีส่วนเติม `returned_date` อยู่ไหม

### บั๊ก 2: `DATE_SUB(CURDATE(), INTERVAL 6 MONTH)` ใช้กับ SQLite ไม่ได้
**สาเหตุ:** โค้ดเดิมเขียนสำหรับ MySQL

**วิธีแก้:** คำนวณวันที่ใน PHP ด้วย `date('Y-m-d', strtotime('-' . self::ARCHIVE_DAYS . ' days'))` แล้วส่งเข้า query

### บั๊ก 3: เงื่อนไข `OR` ไปปนกับตัวกรองวันที่
**สาเหตุ:** ถ้าเขียน `where()->orWhere()->orWhere()` ต่อกันตรง ๆ แล้วตามด้วย `where('event_date', ...)`
SQL จะกลายเป็น `A OR B OR (C AND วันที่)` ทำให้กรองวันที่ไม่ได้ผล

**วิธีแก้:** ห่อ 3 เงื่อนไขไว้ใน `$query->where(function ($q) use ($archiveBefore) { ... })` ให้เป็นวงเล็บเดียวกัน

### บั๊ก 4: กดเรียงลำดับแล้วตัวกรองประเภทหาย
**สาเหตุ:** เขียนลิงก์เป็น `route('home', ['sort' => 'old'])` ซึ่งสร้าง URL ใหม่ที่มีแค่ `sort`
ค่า `type` ที่ผู้ใช้เลือกไว้ก่อนหน้าหายไปทั้งหมด

**วิธีแก้:** เอา query string ปัจจุบันมารวมกับค่าใหม่
```blade
{{ route('home', array_merge(request()->query(), ['sort' => 'old', 'page' => 1])) }}
```
`'page' => 1` บังคับกลับหน้าแรก เพราะพอลำดับเปลี่ยน หน้า 3 เดิมไม่มีความหมายแล้ว

---

## 6. วิธีรัน

หลัง pull ต้องรัน migrate เพราะมีคอลัมน์ใหม่ ไม่งั้นหน้าแรกจะ error `no such column: returned_date`
```bash
php artisan migrate
```

ถ้าต้องการข้อมูลตัวอย่างที่มี `returned_date` ด้วย (ลบข้อมูลเดิมทั้งหมด)
```bash
php artisan migrate:fresh --seed
```

แล้วเข้า `http://127.0.0.1:8000/` และ `http://127.0.0.1:8000/archive`

**ทดสอบแล้ว:**

| เคส | ผล |
|---|---|
| หน้าแรก + หน้าคลัง | 13 + 1 = 14 ครบทุกรายการ |
| กรอง `?type=found` / `?type=lost` | 7 + 6 = 13 ตรงกับจำนวนที่แสดงทั้งหมด |
| `?type=found&sort=old` | ตัวกรองไม่หายตอนเรียงลำดับ แถวแรกเป็นวันที่เก่าสุด |
| แบ่งหน้า | หน้า 1-2 หน้าละ 5 หน้า 3 เหลือ 3 รายการ |
| ตัวนับถอยหลัง | แสดง "อีก 2 วัน" และ "อีก 6 วัน" ใต้สถานะ |
| ช่องค้นหาเร็ว | เด้งไป `/search?searched=1&item_name=...` พร้อมผลลัพธ์ |

---

## 7. สิ่งที่ยังไม่ทำ

- ยังไม่มีโค้ดบันทึก `returned_date` ตอนเปลี่ยนสถานะเป็น "ได้รับคืนแล้ว" (ตอนนี้มีแค่ใน seeder)
- หน้าแรกยังไม่กรองตาม `approval_status` (แสดงทั้งที่อนุมัติแล้วและรออนุมัติ)
- ตัวกรองยังไม่บอกว่ากำลังเลือกอะไรอยู่ (ลิงก์ทุกอันหน้าตาเหมือนกันหมด)
- ลิงก์ "ประเภท" กับ "ช่วงเวลา" ยังล้างค่ากันเอง — มีแค่ "เรียงตาม" ที่ใช้ `array_merge` คงค่าเดิมไว้
- แถบสรุปตัวเลขนับจากทั้งระบบ (14) ไม่เท่ากับจำนวนในตาราง (13) เพราะตารางไม่นับของในคลัง — ตั้งใจให้เป็นภาพรวมทั้งระบบ
- ยังไม่ได้เอาฟอร์มแจ้งของหายมาต่อท้ายหน้าแรก

---

# Folk แก้

งานที่ Folk รับผิดชอบ: **ข้อ 6 User หน่วยงาน** (โพสต์ หลักฐานยืนยัน จุดสถานที่), **ข้อ 7 User แอดมิน** (อนุมัติ ดูข้อมูลหลังบ้าน สถิติ),
ระบบ **login แยกตาม role** และหน้า **แจ้งของหาย** ของ user ทั่วไป

## 1. หน่วยงาน (agency) — ข้อ 6

- เพิ่มคอลัมน์ในตาราง `items` ด้วย migration `2026_09_12_100001_add_report_columns_to_items_table.php`
  - `place_point` จุดสถานที่แบบละเอียด เช่น "ชั้น 3 โซนอ่านเงียบ โต๊ะ B12"
  - `evidence_url`, `evidence_note` รูปและคำอธิบายหลักฐานยืนยัน
- `AgencyUserSeeder.php` บัญชีหน่วยงาน 3 บัญชีในตาราง `finder_users` (role `agency`)
- `AgencyItemController.php` รายการโพสต์ของหน่วยงานตัวเอง / ฟอร์มโพสต์ / บันทึกโพสต์
  (โพสต์ใหม่มีสถานะ `รออนุมัติ` เสมอ)
- หน้า `resources/views/agency/` → `index`, `create`, `show`

## 2. แอดมิน (admin) — ข้อ 7

- เพิ่มคอลัมน์ `approval_status`, `reject_reason`, `approved_by`, `approved_at` ในตาราง `items` (migration เดียวกับข้อ 6)
- `AdminController.php`
  - หน้าข้อมูลหลังบ้าน กรองตามสถานะอนุมัติ / ประเภท / ชื่อสิ่งของ
  - อนุมัติ และไม่อนุมัติ (ต้องกรอกเหตุผล) บันทึกว่าแอดมินคนไหนตรวจ ตรวจเมื่อไหร่
  - หน้าสถิติ: ภาพรวม, สถานะอนุมัติ, แยกตามหมวดหมู่ / สถานที่ / หน่วยงาน, จำนวนผู้ใช้แต่ละ role
- หน้า `resources/views/admin/` → `index`, `stats`
- route ของหน่วยงานและแอดมินแยกไว้ใน `routes/agency_admin.php` แล้ว `require` ต่อท้าย `routes/web.php`

## 3. Login แยกตาม role

login หน้าเดียวสำหรับทุกบัญชี แต่ละบัญชีจะเห็นเฉพาะเมนูและหน้าของ role ตัวเอง

| บัญชี | เมนู | เข้าหน้าของ role อื่น |
|---|---|---|
| ยังไม่ล็อกอิน | หน้าแรก / ค้นหา / เข้าสู่ระบบ | `/agency`, `/admin` → ไปหน้า login |
| user | หน้าแรก / ค้นหา / แจ้งของหาย / โปรไฟล์ / ออกจากระบบ | ถูกส่งกลับหน้าแรก |
| agency | หน้าแรก / ค้นหา / หน่วยงาน / โปรไฟล์ / ออกจากระบบ | ถูกส่งกลับหน้าแรก |
| admin | หน้าแรก / ค้นหา / แอดมิน / สถิติ / โปรไฟล์ / ออกจากระบบ | ถูกส่งกลับหน้าแรก |

**ไฟล์ใหม่**
- `database/migrations/2026_09_16_100001_add_role_to_users_table.php` เพิ่ม `role` (user / agency / admin) และ `finder_user_id`
  ในตาราง `users` ของ Laravel เพื่อผูกบัญชี login เข้ากับข้อมูลคนใน `finder_users`
- `database/seeders/LoginUserSeeder.php` สร้างบัญชี login ให้ทุกคนใน `finder_users` ใช้อีเมลเดียวกัน
- `app/Http/Middleware/EnsureUserIsAgency.php`, `EnsureUserIsAdmin.php` กันไม่ให้ role อื่นเข้าหน้า (ตามตัวอย่าง middleware ใน Laravel Part 03)
- `resources/views/login.blade.php` หน้า login หน้าตาแบบเว็บ KKU Return

**ไฟล์ที่แก้**
- `bootstrap/app.php` ลงทะเบียน alias middleware `agency`, `admin` และให้คนที่ล็อกอินแล้วเปิด `/login` ซ้ำไปหน้าแรก
- `routes/agency_admin.php` ครอบ route ด้วย `middleware(['auth', 'agency'])` และ `middleware(['auth', 'admin'])`
- `AgencyItemController.php`, `AdminController.php` เลิกใช้กล่องเลือกบัญชีแบบ session เปลี่ยนมาใช้ `Auth::user()->finder_user_id`
- หน้า `agency/index`, `admin/index`, `admin/stats` ลบกล่องเลือกบัญชีออก และใช้เมนูกลาง
- `resources/views/partials/menu.blade.php` แสดงเมนูตาม role ด้วย `@guest` / `@auth` และเพิ่มปุ่มออกจากระบบ
- `app/Providers/FortifyServiceProvider.php` ใช้หน้า `login` ของเราแทนหน้า login ของ Laravel
- `config/fortify.php` login เสร็จไปที่ `/` แทน `/dashboard`

## 4. หน้าแจ้งของหาย (user ทั่วไป)

- `resources/views/create.blade.php` ฟอร์มแจ้งของหาย / แจ้งพบของ ที่ `ItemController@create` เรียกใช้แต่ยังไม่มีไฟล์
- ชื่อช่องในฟอร์มตรงกับที่ `ItemController@store` ตรวจ: `postType`, `itemName`, `category`, `location`, `date`,
  `description`, `image`, `reporterName`, `phone`
- ไม่ได้แก้ `ItemController`

ถ้าจะให้รูปที่อัปโหลดแสดงบนเว็บ ต้องรันคำสั่งนี้ครั้งเดียวในเครื่องตัวเอง
```bash
php artisan storage:link
```

**บัญชีทดสอบ** รหัสผ่าน `password` ทุกบัญชี

| role | อีเมล |
|---|---|
| admin | `pimchanok.r@kku.ac.th` |
| agency | `security@kku.ac.th`, `library@kku.ac.th`, `studentaffairs@kku.ac.th` |
| user | `sarath.n@kku.ac.th`, `piyada.p@kkumail.com` และ user อื่นใน `finder_users` |

**ทดสอบแล้ว:** แต่ละ role ล็อกอินแล้วเห็นเมนูถูกต้อง, เข้าหน้าของ role อื่นไม่ได้, ออกจากระบบแล้วกลับเป็นเมนู guest,
user แจ้งของหายสำเร็จและบันทึกลงตาราง `items`, ส่งฟอร์มไม่ครบขึ้นข้อความ error และคงค่าที่กรอกไว้

## 5. สิ่งที่ยังไม่ทำ

- `AgencyItemController` ไม่มีเมธอด `show()` ถ้าหน่วยงานโพสต์เสร็จ หรือกด "รายละเอียด" จะ error
- `LoginUserSeeder` ยังไม่ได้ใส่ใน `DatabaseSeeder` ถ้ามีคนรัน `migrate:fresh --seed` บัญชี login จะหาย
  ต้องรัน `php artisan db:seed --class=LoginUserSeeder` เพิ่มเอง
- `ItemController@store` บันทึก `user_id` เป็น `null` ทำให้ไม่รู้ว่าโพสต์แจ้งของหายเป็นของบัญชีไหน
- route `/posts/create` ยังไม่บังคับ login (ซ่อนไว้แค่ในเมนู)
- หน้าแรกไม่แสดงข้อความ error ตอนถูกส่งกลับเพราะเข้าหน้าของ role อื่น
- หน้าโปรไฟล์ / สมัครสมาชิก ยังเป็นหน้าของ Laravel ต้อง `npm run build` ก่อนถึงจะเปิดได้