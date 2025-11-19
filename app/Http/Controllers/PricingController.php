<?php

namespace App\Http\Controllers;

use App\Domain\Packages\Models\Package;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $packages = Package::active()->get();
        return view('pricing', compact('packages'));
    }
}
