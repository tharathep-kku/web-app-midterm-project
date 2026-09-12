<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
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