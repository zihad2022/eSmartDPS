<?php

namespace App\Livewire;

use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class UserStatusToggle extends Component
{
    public $admin;

    public function mount(Admin $admin)
    {
        $this->admin = $admin;
    }

    public function toggleStatus()
    {
        $this->admin->status = !$this->admin->status;
        $this->admin->save();

        // Optional: emit event for toast notification
        // $this->emit('statusUpdated', $this->admin->id, $this->admin->status);
    }

    public function render()
    {
        return view('livewire.user-status-toggle');
    }
}