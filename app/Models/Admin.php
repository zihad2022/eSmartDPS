<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'username', 'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->profile_photo ? Storage::url($this->profile_photo) : null,
        );
    }
}
