<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Larament\SeoKit\Facades\SeoKit;

class AboutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        SeoKit::title('About');
        return view('about');
    }
}
