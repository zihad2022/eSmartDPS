<?php

namespace App\Http\Requests\Client;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('client')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'project_category_id' => [
                'required',
                'integer',
                Rule::exists('project_categories', 'id')
                    ->where(fn ($query) => $query->where('client_id', owner_client_id())),
            ],
            'investment_amount' => ['required', 'integer', 'min:0'],
            'expected_return' => ['required', 'integer', 'min:0'],
            'expected_return_type' => ['required', Rule::in(['percent', 'amount'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'description' => ['nullable', 'string'],
        ];
    }
}
