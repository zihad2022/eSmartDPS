<?php

namespace App\Models;

use App\Concerns\HasSlug;
use App\Domain\Clients\Models\Client;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'client_id',
        'project_category_id',
        'name',
        'slug',
        'investment_amount',
        'expected_return',
        'expected_return_type',
        'start_date',
        'end_date',
        'description',
        'status',
    ];

    protected $appends = [
        'progress_percent',
        'duration',
        'duration_human',
        'duration_months',
    ];

    protected function casts(): array
    {
        return [
            'investment_amount' => 'integer',
            'expected_return' => 'integer',
            'status' => ProjectStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    protected function sluggable(): string
    {
        return 'name';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projectCategory(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class);
    }

    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProjectStatus::ACTIVE);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', ProjectStatus::COMPLETED);
    }

    /** @deprecated Use completed() instead. */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->completed();
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', ProjectStatus::CANCELLED);
    }

    protected function progressPercent(): Attribute
    {
        return Attribute::get(function (): int {
            if (! $this->start_date || ! $this->end_date) {
                return 0;
            }

            if ($this->end_date->lessThanOrEqualTo($this->start_date)) {
                return now()->greaterThanOrEqualTo($this->end_date) ? 100 : 0;
            }

            if (now()->lessThanOrEqualTo($this->start_date)) {
                return 0;
            }

            if (now()->greaterThanOrEqualTo($this->end_date)) {
                return 100;
            }

            $totalSeconds = $this->start_date->diffInSeconds($this->end_date);
            $elapsedSeconds = $this->start_date->diffInSeconds(now());

            return (int) round(min(100, max(0, ($elapsedSeconds / $totalSeconds) * 100)));
        });
    }

    protected function duration(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->duration_human);
    }

    protected function durationMonths(): Attribute
    {
        return Attribute::get(function (): ?int {
            if (! $this->start_date || ! $this->end_date) {
                return null;
            }

            return (int) $this->start_date->diffInMonths($this->end_date);
        });
    }

    protected function durationHuman(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (! $this->start_date || ! $this->end_date) {
                return null;
            }

            $interval = $this->start_date->diff($this->end_date);
            $parts = [];

            if ($interval->y > 0) {
                $parts[] = $interval->y.' '.str('year')->plural($interval->y);
            }

            if ($interval->m > 0) {
                $parts[] = $interval->m.' '.str('month')->plural($interval->m);
            }

            if ($interval->d > 0 && $interval->y === 0) {
                $parts[] = $interval->d.' '.str('day')->plural($interval->d);
            }

            return $parts ? implode(' ', $parts) : 'Same day';
        });
    }
}
