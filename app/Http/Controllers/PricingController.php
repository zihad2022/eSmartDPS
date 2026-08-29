<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $packages = Package::active()->get();

        return view('pricing', compact('packages'));
    }
}
