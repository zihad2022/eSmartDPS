<?php

namespace App\View\Components\Admin\Layout;

use App\Models\AdminSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class App extends Component
{
    public $title;

    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $user = Auth::guard('admin')->user();
        $settings = AdminSetting::first();

        return view('admin.layout.app', compact('user', 'settings'));
    }
}
