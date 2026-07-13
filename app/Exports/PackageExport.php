<?php

namespace App\Exports;

use App\Domain\Packages\Models\Package;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PackageExport implements FromQuery, WithHeadings, WithMapping
{
    private int $sl = 1;

    public function __construct(private readonly ?string $status = null)
    {
    }

    public function query(): Builder
    {
        return Package::query()
            ->when($this->status === 'active', fn (Builder $query) => $query->active())
            ->when($this->status === 'inactive', fn (Builder $query) => $query->inactive())
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'SL', 'Name', 'Description', 'Price', 'Discount Value', 'Discount Type',
            'Billing Cycle', 'Member Limit', 'User Limit', 'Project Limit', 'Status',
            'Has Trial', 'Trial Days', 'Created At',
        ];
    }

    public function map($package): array
    {
        return [
            $this->sl++,
            $package->name,
            $package->description,
            $package->price,
            $package->discount_value,
            $package->discount_type?->label() ?? 'N/A',
            $package->billing_cycle?->label() ?? 'N/A',
            $package->member_limit,
            $package->user_limit === null ? 'Unlimited' : $package->user_limit,
            $package->project_limit,
            $package->is_active ? 'Active' : 'Inactive',
            $package->has_trial ? 'Yes' : 'No',
            $package->trial_days,
            $package->created_at?->format('Y-m-d'),
        ];
    }
}
