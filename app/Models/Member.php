<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Member extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'client_id',
        'member_id',
        'name',
        'email',
        'phone',
        'pin',
        'status',
    ];

    protected $hidden = [
        'pin',
        'remember_token',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($member) {
            $member->member_id = generate_member_id();
        });
    }

    /*--------------------------------
    | ACCESSORS (Computed Attributes)
    |--------------------------------*/

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->profile_photo ? Storage::url($this->profile_photo) : null
        );
    }
}
