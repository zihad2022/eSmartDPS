<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPakage extends Model
{
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function pakage()
    {
        return $this->belongsTo(Pakage::class);
    }
}
