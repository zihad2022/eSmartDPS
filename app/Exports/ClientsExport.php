<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientsExport implements FromQuery, WithHeadings, WithMapping
{
    private int $sl = 1;

    public function __construct(private readonly ?string $status = null) {}

    public function query(): Builder
    {
        return Client::query()
            ->parents()
            ->with('latestClientPackage.package')
            ->when($this->status === 'active', fn (Builder $query) => $query->active())
            ->when($this->status === 'inactive', fn (Builder $query) => $query->inactive())
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'SL', 'User ID', 'First Name', 'Last Name', 'Email', 'Phone Number',
            'Division', 'District', 'Address', 'Postal Code', 'Role', 'Status',
            'Subscription Plan', 'Created At',
        ];
    }

    public function map($client): array
    {
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
            $client->status ? 'Active' : 'Inactive',
            $client->latestClientPackage?->package?->name ?? 'N/A',
            $client->created_at?->format('Y-m-d'),
        ];
    }
}
