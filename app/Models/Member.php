<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Member extends Authenticatable
{
    use Notifiable;

    /**
     * Mass assignable attributes.
     * These fields can be set via create(), update(), or fill().
     */
    protected $fillable = [
        'client_id',     // Parent client reference
        'member_id',     // Unique member code (auto-generated)
        'name',          // Member's full name
        'email',         // Member's email
        'phone',         // Member's phone number
        'pin',           // Secure PIN for authentication
        'status',        // Account status (true = active, false = inactive)
    ];

    /**
     * Hidden attributes for arrays & JSON.
     * These will not be visible in API responses or toArray().
     */
    protected $hidden = [
        'pin',            // Security measure to hide PIN
        'remember_token', // Laravel's "remember me" token
    ];

    /*--------------------------------
    | RELATIONSHIPS
    |--------------------------------*/

    /**
     * Each member belongs to a single client (owner).
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * A member can have multiple payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /*--------------------------------
    | QUERY SCOPES
    |--------------------------------*/

    /**
     * Scope: Only active members (status = true).
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Only inactive members (status = false).
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /*--------------------------------
    | MODEL EVENTS
    |--------------------------------*/

    /**
     * Booted method runs after the model is initialized.
     * We use it to automatically set the member_id on creation.
     */
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($member) {
            // Auto-generate a unique member ID before saving
            $member->member_id = generate_member_id();
        });
    }

    /*--------------------------------
    | ACCESSORS (Computed Attributes)
    |--------------------------------*/

    /**
     * profile_photo_url attribute.
     * Returns the full public URL of the member's profile photo,
     * or null if no photo is uploaded.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->profile_photo ? Storage::url($this->profile_photo) : null
        );
    }
}
