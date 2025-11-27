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

            /**
             * Personal Information
             */
            'first_name'       => ['required', 'string', 'max:255'],
            'last_name'        => ['required', 'string', 'max:255'],
            'profile_photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            /**
             * Authentication
             */
            // 'user_id'          => [
            //     $ignoreId ? 'sometimes' : 'required',
            //     'string',
            //     Rule::unique('clients', 'user_id')->ignore($ignoreId),
            // ],
            'password'         => [$ignoreId ? 'nullable' : 'required', 'string', 'min:6'],

            /**
             * Contact Information
             */
            'email'            => [
                $ignoreId ? 'sometimes' : 'required',
                'email',
                Rule::unique('clients', 'email')->ignore($ignoreId),
            ],
            'phone'            => [
                $ignoreId ? 'sometimes' : 'required',
                'string',
                'max:20',
                Rule::unique('clients', 'phone')->ignore($ignoreId),
            ],
            'nid_number'       => ['nullable', 'string', 'max:50'],
            'nid_card_front'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'nid_card_back'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'division'         => ['nullable', 'string', 'max:255'],
            'district'         => ['nullable', 'string', 'max:255'],
            'address'          => ['nullable', 'string'],
            'postal_code'      => ['nullable', 'string', 'max:20'],

            /**
             * Role & Status
             */
            'role'             => [
                'required',
                Rule::in(['admin', 'manager', 'editor'])
            ],
            'status'           => ['required', 'boolean'],
        ];
    }
}
