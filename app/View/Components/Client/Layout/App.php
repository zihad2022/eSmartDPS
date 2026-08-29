<?php

namespace App\View\Components\Client\Layout;

use App\Models\ClientSetting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class App extends Component
{
    public $title;

    /**
     * Create a new component instance.
     */
    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $user = Auth::guard('client')->user();
        $settings = ClientSetting::where('client_id', owner_client_id())->first();

        return view('client.layout.app', compact('settings', 'user'));
    }
}
