<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clientId = $this->route('client')->id ?? null;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],

            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('clients', 'email')->ignore($clientId),
            ],

            'phone' => [
                'nullable', 'string', 'max:20',
                Rule::unique('clients', 'phone')->ignore($clientId),
            ],

            'password' => [
                $clientId ? 'nullable' : 'required',
                'string', 'min:6',
            ],

            'profile_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],

            'nid_number' => ['nullable', 'string', 'max:50'],
            'nid_card_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'nid_card_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],

            'division' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:20'],

            'status' => ['required', 'boolean'],

            'pakage_id' => ['required', 'exists:pakages,id'],
        ];
    }
}
