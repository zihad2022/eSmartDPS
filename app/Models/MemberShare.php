<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberShare extends Model
{
    protected $fillable = [
        'member_id',
        'share_id',
        'shares_count',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function share(): BelongsTo
    {
        return $this->belongsTo(Share::class);
    }
}
