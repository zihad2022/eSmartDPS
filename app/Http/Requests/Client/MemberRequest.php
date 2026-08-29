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
        return auth('client')->check();
    }

    public function rules(): array
    {
        $member = $this->route('member');
        $memberId = is_object($member) ? $member->id : $member;
        $clientId = owner_client_id();
        $settings = Client::findOrFail($clientId)->settings;

        if (! $settings) {
            throw ValidationException::withMessages([
                'share_quantity' => 'Your account settings are not properly configured. Please contact support.',
            ]);
        }

        $minShare = $settings->minimum_shares ?? 1;
        $maxShare = $settings->maximum_shares ?? 999;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('members', 'email')
                    ->where(fn ($query) => $query->where('client_id', $clientId))
                    ->ignore($memberId),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'boolean'],
            'share_quantity' => ['required', 'integer', "min:{$minShare}", "max:{$maxShare}"],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
                'min:6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'share_quantity.min' => 'Share quantity must be at least :min (according to your settings).',
            'share_quantity.max' => 'Share quantity cannot exceed :max (according to your settings).',
        ];
    }
}
