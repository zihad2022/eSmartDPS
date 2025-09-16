<?php

namespace App\Http\Requests\Client;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        // -----------------------------
        // 1. Only logged-in clients can manage members
        // -----------------------------
        return auth('client')->check();
    }

    public function rules(): array
    {
        // -----------------------------
        // 2. Get current member ID for unique validation (on update)
        // -----------------------------
        $memberId = $this->route('member')?->id;

        // -----------------------------
        // 3. Always use the main owner's settings
        // -----------------------------
        $client   = Client::findOrFail(owner_client_id());
        $settings = $client?->settings;

        // -----------------------------
        // 4. Handle missing settings gracefully
        // -----------------------------
        if (! $settings) {
            throw ValidationException::withMessages([
                'share_quantity' => 'Your account settings are not properly configured. Please contact support.',
            ]);
        }

        $minShare = $settings->minimum_shares ?? 1;   // Default fallback
        $maxShare = $settings->maximum_shares ?? 999; // Default fallback

        // -----------------------------
        // 5. Define validation rules
        // -----------------------------
        $rules = [
            'name'           => ['required', 'string', 'max:255'], 
            'email'          => ['nullable', 'email', Rule::unique('members', 'email')->ignore($memberId)],
            'phone'          => ['nullable', 'string', 'max:20'],
            'status'         => ['required', 'boolean'],
            'share_quantity' => ['required', 'integer', "min:$minShare", "max:$maxShare"],
        ];

        // -----------------------------
        // 6. Password: required on create, optional on update
        // -----------------------------
        $rules['password'] = $this->isMethod('post') 
            ? ['required', 'string', 'min:6'] 
            : ['nullable', 'string', 'min:6'];

        return $rules;
    }

    public function messages(): array
    {
        // -----------------------------
        // 7. Custom error messages
        // -----------------------------
        return [
            'share_quantity.min' => "Share quantity must be at least :min (according to your settings).",
            'share_quantity.max' => "Share quantity cannot exceed :max (according to your settings).",
        ];
    }
}
