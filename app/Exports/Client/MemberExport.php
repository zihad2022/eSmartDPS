<?php

namespace App\Exports\Client;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class MemberExport
 *
 * This class is responsible for exporting members' data into an Excel file.
 * It supports filtering by status (active, inactive, or all).
 */
class MemberExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Serial number counter (used for SL column).
     *
     * @var int
     */
    private int $sl = 1;

    /**
     * Member status filter.
     * Can be: "active", "inactive", or null (for all).
     *
     * @var string|null
     */
    private ?string $status = null;

    /**
     * Constructor to set the status filter.
     *
     * @param string|null $status
     */
    public function __construct(?string $status = null)
    {
        $this->status = $status;
    }

    /**
     * Fetch the data collection to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->status === 'active') {
            return Member::active()->get();
        }

        if ($this->status === 'inactive') {
            return Member::inactive()->get();
        }

        // If no status filter provided, return all members
        return Member::all();
    }

    /**
     * Define the Excel file headings (columns).
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'SL',              // Serial number
            'Member ID',       // Unique member identifier
            'Name',            // Member full name
            'Email',           // Member email address
            'Phone Number',    // Member contact number
            'Status',          // Active or Inactive
            'Share Quantity',  // Number of shares owned
            'Total Balance',   // Account balance
            'Created At',      // Date member was created
        ];
    }

    /**
     * Map each member's data into the desired row format.
     *
     * @param \App\Models\Member $member
     * @return array
     */
    public function map($member): array
    {
        return [
            '#' . $this->sl++,                            // Auto-incremented serial number
            $member->member_id,                           // Member ID
            $member->name,                                // Member Name
            $member->email,                               // Email
            $member->phone,                               // Phone Number
            $member->status == 1 ? 'Active' : 'Inactive', // Status (1 = Active, 0 = Inactive)
            $member->share_quantity,                      // Share Quantity
            $member->total_balance,                       // Total Balance
            $member->created_at->format('Y-m-d'),         // Formatted Created Date
        ];
    }
}
