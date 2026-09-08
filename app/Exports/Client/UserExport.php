<?php

namespace App\Exports\Client;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    private int $sl = 1;

    private ?string $role;

    private ?string $status;

    public function __construct(?string $role = null, ?string $status = null)
    {
        $this->role = $role;
        $this->status = $status;
    }

    /**
     * Return the collection of users to export
     */
    public function collection(): \Illuminate\Support\Collection
    {
        $parent = Client::findOrFail(owner_client_id());

        // Build children query
        $query = $parent->children();

        if ($this->role) {
            $query->where('role', $this->role);
        }

        if (! is_null($this->status)) {
            if ($this->status == 'active') {
                $query->where('status', 1);
            } elseif ($this->status == 'inactive') {
                $query->where('status', 0);
            }
        }

        $users = $query->get();

        // Include parent if it matches filters
        $includeParent = true;

        if ($this->role && $parent->role !== $this->role) {
            $includeParent = false;
        }

        if (! is_null($this->status) && $parent->status != $this->status) {
            $includeParent = false;
        }

        if ($includeParent) {
            $users = collect([$parent])->merge($users);
        }

        return $users;
    }

    /**
     * Set the headings for the Excel sheet
     */
    public function headings(): array
    {
        return [
            'SL',
            'User ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'NID Number',
            'NID Front',
            'NID Back',
            'Division',
            'District',
            'Address',
            'Postal Code',
            'Role',
            'Status',
            'Created At',
        ];
    }

    /**
     * Map each user object to the Excel row
     */
    public function map($user): array
    {
        return [
            $this->sl++,
            $user->user_id,
            $user->first_name ?? 'N/A',
            $user->last_name ?? 'N/A',
            $user->email ?? 'N/A',
            $user->phone ?? 'N/A',
            $user->nid_number ?? 'N/A',
            $user->nid_card_front ?? 'N/A',
            $user->nid_card_back ?? 'N/A',
            $user->division ?? 'N/A',
            $user->district ?? 'N/A',
            $user->address ?? 'N/A',
            $user->postal_code ?? 'N/A',
            ucfirst($user->role),
            $user->status ? 'Active' : 'Inactive',
            $user->created_at->format('Y-m-d'),
        ];
    }
}
