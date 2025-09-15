<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('client')->check(); // Only logged-in clients can manage members
    }

    public function rules(): array
    {
        $memberId = $this->route('member')?->id; // Get current member ID for unique checks

        $client = auth('client')->user(); // Current authenticated client
        $settings = $client?->settings; // Client settings for share limits
        $minShare = $settings?->minimum_shares;
        $maxShare = $settings?->maximum_shares;

        $rules = [
            'name'           => ['required', 'string', 'max:255'], // Member full name
            'email'          => ['nullable', 'email', Rule::unique('members', 'email')->ignore($memberId)], // Unique email
            'phone'          => ['nullable', 'string', 'max:20'], // Optional phone
            'status'         => ['required', 'boolean'], // Active or inactive
            'share_quantity' => ['required', 'integer', "min:$minShare", "max:$maxShare"], // Shares within client limits
        ];

        // Password required on create, optional on update
        $rules['password'] = $this->isMethod('post') ? ['required', 'string', 'min:6'] : ['nullable', 'string', 'min:6'];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'share_quantity.min' => "Share quantity must be at least :min (according to your settings).",
            'share_quantity.max' => "Share quantity cannot exceed :max (according to your settings).",
        ];
    }
}
