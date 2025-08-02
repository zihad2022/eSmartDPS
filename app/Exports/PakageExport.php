<?php

namespace App\Exports;

use App\Models\Pakage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PakageExport implements FromCollection, WithHeadings, WithMapping
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
            return Pakage::active()->get();
        }
        if ($this->status === 'inactive') {
            return Pakage::inactive()->get();
        }

        return Pakage::all();
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

    public function map($pakage): array
    {
        return [
            $this->sl++,
            $pakage->name,
            $pakage->description,
            $pakage->price,
            $pakage->discount_value,
            $pakage->discount_type,
            $pakage->billing_cycle,
            $pakage->member_limit,
            $pakage->user_limit,
            $pakage->project_limit,
            $pakage->is_active ? 'Active' : 'Inactive',
            $pakage->has_trial ? 'Yes' : 'No',
            $pakage->trial_days,
            $pakage->created_at,
        ];
    }
}
