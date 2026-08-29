<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Member extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'client_id',
        'member_id',
        'name',
        'email',
        'phone',
        'password',
        'status',
        'profile_photo',
        'share_quantity',
        'total_balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => 'boolean',
            'share_quantity' => 'integer',
            'total_balance' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Member $member): void {
            if (blank($member->member_id)) {
                $member->member_id = generate_member_id();
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->profile_photo ? Storage::url($this->profile_photo) : null);
    }
}
