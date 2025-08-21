<?php

namespace App\Exports;

use App\Models\Package;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PackageExport implements FromCollection, WithHeadings, WithMapping
{
    private int $sl = 1;

    private ?string $status = null;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function collection()
    {
        if ($this->status === 'active') {
            return Package::active()->get();
        }
        if ($this->status === 'inactive') {
            return Package::inactive()->get();
        }

        return Package::all();
    }

    public function headings(): array
    {
        return [
            'sl',
            'Name',
            'Description',
            'Price',
            'Discount Value',
            'Discount Type',
            'Billing Cycle',
            'Member Limit',
            'User Limit',
            'Project Limit',
            'Is Active',
            'Has Trial',
            'Trial Days',
            'Created At',
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
            $package->discount_type->label(),
            $package->billing_cycle->label(),
            $package->member_limit,
            $package->user_limit,
            $package->project_limit,
            $package->is_active ? 'Active' : 'Inactive',
            $package->has_trial ? 'Yes' : 'No',
            $package->trial_days,
            $package->created_at,
        ];
    }
}
