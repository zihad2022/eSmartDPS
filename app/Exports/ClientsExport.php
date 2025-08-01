<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromCollection, WithHeadings, WithMapping
{
    private int $sl = 1;

    public function collection()
    {
        return Client::all();
    }

    public function headings(): array
    {
        return [
            'sl',
            'User ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone Number',
            'Division',
            'District',
            'Address',
            'Postal Code',
            'Role',
            'Status',
            'Subscription Plan',
            'Created At',
        ];
    }

    public function map($client): array
    {
        $client->load('ClientSubscription.subscription');

        return [
            '#'.$this->sl++,
            $client->user_id,
            $client->first_name,
            $client->last_name,
            $client->email,
            $client->phone,
            $client->division,
            $client->district,
            $client->address,
            $client->postal_code,
            $client->role,
            $client->status == 1 ? 'Active' : 'Inactive',
            $client->ClientSubscription->subscription->name ?? 'N/A',
            $client->created_at->format('Y-m-d'),
        ];
    }
}
