<?php

namespace App\Exports\Admin;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromQuery, WithHeadings, WithMapping
{
    private int $sl = 1;

    public function __construct(private readonly ?string $status = null) {}

    public function query(): Builder
    {
        return Admin::query()
            ->with('roles:id,name')
            ->when($this->status === 'active', fn (Builder $query) => $query->active())
            ->when($this->status === 'inactive', fn (Builder $query) => $query->inactive())
            ->orderBy('id');
    }

    public function headings(): array
    {
        return ['SL', 'Name', 'Username', 'Email', 'Phone', 'Role', 'Status', 'Last Login', 'Created At'];
    }

    public function map($admin): array
    {
        return [
            '#'.$this->sl++,
            $admin->name,
            $admin->username,
            $admin->email,
            $admin->phone,
            $admin->roles->pluck('name')->join(', ') ?: 'Unassigned',
            $admin->status ? 'Active' : 'Inactive',
            $admin->last_login?->format('Y-m-d H:i:s') ?? 'Never',
            $admin->created_at?->format('Y-m-d'),
        ];
    }
}
