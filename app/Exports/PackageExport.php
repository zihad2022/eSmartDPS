<?php

namespace App\Exports;

use App\Models\Package;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PackageExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Serial counter for each row in the Excel export.
     */
    private int $sl = 1;

    /**
     * The status filter for the packages (active, inactive, or all).
     */
    private ?string $status = null;

    /**
     * Constructor to initialize the status filter.
     *
     * @param string|null $status
     */
    public function __construct(?string $status)
    {
        $this->status = $status;
    }

    /**
     * Fetch the collection of packages based on the given status.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->status === 'active') {
            // Return only active packages
            return Package::active()->get();
        }

        if ($this->status === 'inactive') {
            // Return only inactive packages
            return Package::inactive()->get();
        }

        // If no filter is applied, return all packages
        return Package::all();
    }

    /**
     * Define the headings (column titles) for the Excel export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Sl',
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

    /**
     * Map the data of each package into a row for the Excel sheet.
     *
     * @param \App\Models\Package $package
     * @return array
     */
    public function map($package): array
    {
        return [
            // Auto increment serial number
            $this->sl++,

            // Basic package details
            $package->name,
            $package->description,
            $package->price,

            // Discount details
            $package->discount_value,
            $package->discount_type->label(),

            // Billing cycle (Monthly, Yearly, etc.)
            $package->billing_cycle->label(),

            // Limits
            $package->member_limit,
            $package->user_limit,
            $package->project_limit,

            // Status information
            $package->is_active ? 'Active' : 'Inactive',
            $package->has_trial ? 'Yes' : 'No',
            $package->trial_days,

            // Created at timestamp
            $package->created_at,
        ];
    }
}
