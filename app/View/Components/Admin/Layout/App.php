<?php

namespace App\View\Components\Admin\Layout;

use App\Actions\Admin\Layout\GetAdminLayoutDataAction;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class App extends Component
{
    public function __construct(public string $title = '') {}

    public function render(): View|Closure|string
    {
        return view('admin.layout.app', app(GetAdminLayoutDataAction::class)->execute());
    }
}
