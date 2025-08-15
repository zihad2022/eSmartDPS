<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        // If only logged-in client can create/update members
        return auth('client')->check();
    }

    public function rules(): array
    {
        $memberId = $this->route('member')?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                Rule::unique('members', 'email')->ignore($memberId),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'boolean'],
        ];

        if ($this->isMethod('post')) { // Store
            $rules['member_id'] = ['required', 'string', 'unique:members,member_id'];
            $rules['password'] = ['required', 'string', 'min:6'];
        } else { // Update
            $rules['password'] = ['nullable', 'string', 'min:6'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
        ];
    }
}
