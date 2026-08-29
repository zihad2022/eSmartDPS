<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow this request
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            /**
             * Parent Client
             */
            'parent_id' => ['nullable', 'exists:clients,id'],

            /**
             * Authentication
             */
            // 'user_id'       => ['required', 'string', 'max:255', 'unique:clients,user_id'], Auto Generate
            'password' => ['required', 'string', 'min:6', 'max:255'],

            /**
             * Personal Information
             */
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'max:2048'], // 2MB

            /**
             * Contact Information
             */
            'email' => ['required', 'email', 'unique:clients,email'],
            'phone' => ['required', 'string', 'unique:clients,phone'],

            /**
             * Identity / NID
             */
            'nid_number' => ['nullable', 'string', 'max:50'],
            'nid_card_front' => ['nullable', 'image', 'max:4096'],
            'nid_card_back' => ['nullable', 'image', 'max:4096'],

            /**
             * Location
             */
            'division' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:20'],

            /**
             * Role & Permissions
             */
            // 'role'          => ['required', 'in:super-admin,admin,manager,editor'], Auto Set

            /**
             * Status
             */
            'status' => ['nullable', 'boolean'],
        ];
    }
}
