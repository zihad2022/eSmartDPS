<?php

namespace App\Exports;

use App\Domain\Clients\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Counter for serial number (SL column).
     */
    private int $sl = 1;

    /**
     * Status filter for clients.
     * Can be: "active", "inactive", or null (all clients).
     */
    private ?string $status = null;

    /**
     * Constructor to set status filter.
     */
    public function __construct(?string $status = null)
    {
        $this->status = $status;
    }

    /**
     * Fetch the client collection based on the status filter.
     */
    public function collection()
    {
        if ($this->status === 'active') {
            return Client::active()->parents()->get();
        }

        if ($this->status === 'inactive') {
            return Client::inactive()->parents()->get();
        }

        // Default: return all parent clients if no status is given
        return Client::parents()->get();
    }

    /**
     * Define the column headings for the Excel export.
     */
    public function headings(): array
    {
        return [
            'SL',                 // Serial number
            'User ID',            // Unique ID for the client
            'First Name',         // Client's first name
            'Last Name',          // Client's last name
            'Email',              // Client's email address
            'Phone Number',       // Contact number
            'Division',           // Client's division (region)
            'District',           // Client's district (area)
            'Address',            // Full address
            'Postal Code',        // Postal/zip code
            'Role',               // User role (client, parent, etc.)
            'Status',             // Active or Inactive
            'Subscription Plan',  // Latest subscription package name
            'Created At',         // Account creation date
        ];
    }

    /**
     * Map each client model into a row of data for Excel.
     */
    public function map($client): array
    {
        // Eager load the latest subscription package relationship
        $client->load('latestClientPackage.package');

        return [
            '#' . $this->sl++,                                   // SL number with #
            $client->user_id,                                    // User ID
            $client->first_name,                                 // First name
            $client->last_name,                                  // Last name
            $client->email,                                      // Email
            $client->phone,                                      // Phone number
            $client->division,                                   // Division
            $client->district,                                   // District
            $client->address,                                    // Address
            $client->postal_code,                                // Postal code
            $client->role,                                       // Role
            $client->status == 1 ? 'Active' : 'Inactive',        // Status label
            $client->latestClientPackage->package->name ?? 'N/A',// Subscription plan name (or N/A)
            $client->created_at->format('Y-m-d'),                // Account creation date
        ];
    }
}
