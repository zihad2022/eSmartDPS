<?php

namespace App\Models;

use App\Concerns\HasSlug;
use App\Enums\ProjectStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasSlug;

    protected $fillable = [
        'client_id',
        'name',
        'slug',
        'type',
        'investment_amount',
        'expected_return',
        'start_date',
        'end_date',
        'description',
    ];

    public function sluggable(): string
    {
        return 'name';
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    protected $appends = ['progress_percent', 'duration_human'];

    /**
     * Get the progress percent attribute.
     *
     * @return float|int
     */
    public function getProgressPercentAttribute(): int
    {
        if (! $this->start_date || ! $this->end_date) {
            return 0;
        }

        $now = Carbon::now();
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);

        if ($now->lt($start)) {
            return 0;
        }

        if ($now->gt($end)) {
            return 100;
        }

        $totalDuration = $end->diffInSeconds($start);
        $elapsed = $now->diffInSeconds($start);

        return (int) round(($elapsed / $totalDuration) * 100);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ProjectStatus::ACTIVE->value);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', ProjectStatus::COMPLETED->value);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', ProjectStatus::CANCELLED->value);
    }
}
