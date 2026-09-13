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
