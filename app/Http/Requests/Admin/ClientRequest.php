<?php

namespace App\Http\Requests\Admin;

use App\Domain\Clients\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        /** @var Client|null $client */
        $client = $this->route('client');

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('clients', 'email')->ignore($client?->id),
            ],
            'phone' => [
                'nullable', 'string', 'max:30',
                Rule::unique('clients', 'phone')->ignore($client?->id),
            ],
            'password' => [
                $client ? 'nullable' : 'required',
                'string', Password::min(8),
            ],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'nid_number' => ['nullable', 'string', 'max:100'],
            'nid_card_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'nid_card_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'package_id' => ['required', 'integer', Rule::exists('packages', 'id')],
            'division' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:2000'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'boolean'],
        ];
    }
}
