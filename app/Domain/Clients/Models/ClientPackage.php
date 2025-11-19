<?php

namespace App\Domain\Clients\Models;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use Illuminate\Database\Eloquent\Model;

class ClientPackage extends Model
{
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
