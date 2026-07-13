<?php

namespace App\Livewire;

use App\Actions\Admin\Users\ToggleAdminStatusAction;
use App\Models\Admin;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UserStatusToggle extends Component
{
    public Admin $admin;

    public function mount(Admin $admin): void
    {
        $this->admin = $admin;
    }

    public function toggleStatus(ToggleAdminStatusAction $action): void
    {
        $actor = auth('admin')->user();
        abort_unless($actor && $actor->can('edit users'), 403);

        try {
            $this->admin = $action->execute($actor, $this->admin);
            $this->dispatch('notify', 'User status updated successfully.');
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first();
            $this->addError('status', $message);
            $this->dispatch('notify', $message, 'error');
        }
    }

    public function render()
    {
        return view('livewire.user-status-toggle');
    }
}
