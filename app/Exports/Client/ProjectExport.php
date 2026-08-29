<?php

namespace App\Exports\Client;

use App\Models\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectExport implements FromCollection, WithHeadings, WithMapping
{
    private int $sl = 1;

    private ?string $status = null;

    public function __construct(?string $status = null)
    {
        $this->status = $status;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        if ($this->status === 'active') {
            return Project::active()->where('client_id', owner_client_id())->get();
        }

        if ($this->status === 'completed') {
            return Project::completed()->where('client_id', owner_client_id())->get();
        }

        if ($this->status === 'cancelled') {
            return Project::cancelled()->where('client_id', owner_client_id())->get();
        }

        // If no status filter provided, return all projects
        return Project::where('client_id', owner_client_id())->get();
    }

    public function headings(): array
    {
        return [
            'SL',
            'Name',
            'Category',
            'Invested Amount',
            'Expected Return',
            'Start Date',
            'End Date',
            'Duration',
            'Description',
            'Status',
            'Created At',
        ];
    }

    public function map($project): array
    {
        $project->load('projectCategory');

        return [
            $this->sl++,
            $project->name,
            $project->projectCategory->name,
            $project->investment_amount,
            $project->expected_return,
            $project->start_date->format('Y-m-d'),
            $project->end_date->format('Y-m-d'),
            $project->duration,
            $project->description,
            $project->status->label(),
            $project->created_at->format('Y-m-d'),
        ];
    }
}
