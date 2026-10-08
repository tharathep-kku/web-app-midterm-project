<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<FinderUser, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(FinderUser::class, 'user_id');
    }

    /**
     * @return BelongsTo<ReturnUnit, $this>
     */
    public function returnUnit(): BelongsTo
    {
        return $this->belongsTo(ReturnUnit::class);
    }

    protected $fillable = [
        'user_id',
        'category_id',
        'return_unit_id',
        'deposit_image_url',
        'type',
        'title',
        'description',
        'color',
        'brand',
        'location',
        'event_date',
        'image_url',
        'status',
        'returned_date',
        'place_point',
        'evidence_url',
        'evidence_note',
        'approval_status',
        'reject_reason',
        'approved_by',
        'approved_at',
        'reporter_name',
        'reporter_phone',
    ];

    // ส่งหลักฐานการคืนได้จนกว่าจะส่งแล้ว (รอแอดมินยืนยัน) หรือคืนเรียบร้อย
    // ส่งแล้วก็แก้ไขโพสต์ไม่ได้ด้วย เพื่อไม่ให้ข้อมูลเปลี่ยนระหว่างแอดมินตรวจ
    public function canSubmitEvidence(): bool
    {
        return ! in_array($this->status, ['รอแอดมินยืนยัน', 'ได้รับคืนแล้ว', 'ได้รับของแล้ว'], true);
    }

    public function canEdit(): bool
    {
        return $this->canSubmitEvidence();
    }

    // ลบโพสต์พร้อมไฟล์ที่ผู้ใช้อัปโหลด (เฉพาะไฟล์ใน storage/ รูปตัวอย่างใน images/ ใช้ร่วมกันห้ามลบ)
    public function deleteWithFiles(): void
    {
        foreach ([$this->image_url, $this->evidence_url] as $path) {
            if ($path && str_starts_with($path, 'storage/')) {
                Storage::disk('public')->delete(substr($path, strlen('storage/')));
            }
        }

        $this->delete();
    }
}
