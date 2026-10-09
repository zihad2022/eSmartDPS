<?php

namespace App\View\Components\Member\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class App extends Component
{
    public string $title;

    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    public function render(): View|Closure|string
    {
        return view('member.layout.app');
    }
}
