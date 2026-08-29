<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Larament\SeoKit\Facades\SeoKit;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        SeoKit::title('Home');

        return view('home');
    }
}
