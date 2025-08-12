<?php

namespace App\Exports\Admin;

use App\Models\Admin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    private int $sl = 1;

    private ?string $status = null;

    public function __construct($status = null)
    {
        $this->status = $status;
    }

    public function collection()
    {
        $query = Admin::query();

        if ($this->status === 'active') {
            $query->active();
        } elseif ($this->status === 'inactive') {
            $query->inactive();
        }

        return $query->get();
    }

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

    public function map($admin): array
    {
        return [
            '#'.$this->sl++,
            $admin->name,
            $admin->username,
            $admin->email,
            $admin->phone,
            $admin->profile_photo ?? 'N/A',
            $admin->status ? 'Active' : 'Inactive',
            $admin->created_at->format('Y-m-d'),
        ];
    }
}
