<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ignoreId = $this->route('user')?->id; // For update

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                $ignoreId ? 'sometimes' : 'required',
                'email',
                Rule::unique('clients', 'email')->ignore($ignoreId),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => [$ignoreId ? 'nullable' : 'required', 'string', 'min:6'],
            'division' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
            'role' => ['required', Rule::in(['admin', 'manager', 'editor'])],
            'status' => ['required', 'boolean'],
        ];
    }
}
