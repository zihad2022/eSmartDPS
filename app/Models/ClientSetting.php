<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSetting extends Model
{
    protected $fillable = [
        'client_id',
        'organization_name',
        'short_name',
        'contact_email',
        'contact_phone',
        'address',
        'currency',
    ];
}
