<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

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
            'project_category_id' => ['required', 'exists:project_categories,id'],
            'investment_amount' => ['required', 'numeric', 'min:0'],
            'expected_return' => ['required', 'numeric', 'min:0'],
            'expected_return_type' => ['required', 'string', 'in:percent,amount'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
        ];
    }
}
