<?php

namespace App\Exports\Admin;

use App\Models\Admin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class UserExport
 *
 * This class is responsible for exporting Admin users to Excel.
 * It supports filtering users by status (active or inactive).
 * Each exported row contains user information with a serial number (SL).
 */
class UserExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Running serial number counter for each row in export.
     *
     * @var int
     */
    private int $sl = 1;

    /**
     * Filter status value (active, inactive, or null for all users).
     *
     * @var string|null
     */
    private ?string $status = null;

    /**
     * Constructor to set the status filter.
     *
     * @param string|null $status
     */
    public function __construct($status = null)
    {
        $this->status = $status;
    }

    /**
     * Return the collection of Admin users for export.
     * Applies filtering based on the status (active/inactive).
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Admin::query();

        // Apply filter if status is provided
        if ($this->status === 'active') {
            $query->active(); // Assuming "active" scope exists in Admin model
        } elseif ($this->status === 'inactive') {
            $query->inactive(); // Assuming "inactive" scope exists in Admin model
        }

        return $query->get();
    }

    /**
     * Define the column headings for the exported Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'SL',
            'Name',
            'Username',
            'Email',
            'Phone',
            'Profile Photo',
            'Status',
            'Created At',
        ];
    }

    /**
     * Map each Admin user into a row for Excel.
     *
     * @param \App\Models\Admin $admin
     * @return array
     */
    public function map($admin): array
    {
        return [
            '#' . $this->sl++,                         // Serial number with #
            $admin->name,                              // Full name
            $admin->username,                          // Username
            $admin->email,                             // Email address
            $admin->phone,                             // Phone number
            $admin->profile_photo ?? 'N/A',            // Profile photo or N/A if missing
            $admin->status ? 'Active' : 'Inactive',    // Status as human readable
            $admin->created_at->format('Y-m-d'),       // Created date in YYYY-MM-DD format
        ];
    }
}
