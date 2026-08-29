<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Larament\SeoKit\Facades\SeoKit;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        SeoKit::title('Home');

        return view('home');
    }
}
