<?php

namespace App\Models;

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
}
    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'title',
        'description',
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
}
