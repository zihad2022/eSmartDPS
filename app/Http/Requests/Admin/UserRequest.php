<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        /** @var Admin|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:100',
                Rule::unique('admins', 'username')->ignore($user?->id),
            ],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('admins', 'email')->ignore($user?->id),
            ],
            'phone' => [
                'required', 'string', 'max:30',
                Rule::unique('admins', 'phone')->ignore($user?->id),
            ],
            'password' => [
                $user ? 'nullable' : 'required',
                'string', Password::min(8),
            ],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'role' => [
                'required',
                Rule::exists('roles', 'name')->where('guard_name', 'admin'),
            ],
            'status' => ['required', 'boolean'],
        ];
    }
}
