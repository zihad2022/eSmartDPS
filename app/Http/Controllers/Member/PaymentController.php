<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function create()
    {
        return view('member.payment');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'payment_notes' => 'nullable|string',
            'receipt_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        // Save file
        $path = $request->file('receipt_file')->store('receipts', 'public');

        // Store in database
        Payment::create([
            'amount' => $validated['payment_amount'],
            'date' => $validated['payment_date'],
            'method' => $validated['payment_method'],
            'reference' => $validated['reference_number'],
            'notes' => $validated['payment_notes'],
            'receipt_path' => $path,
        ]);

        return back()->with('success', 'Payment submitted successfully!');
    }
}
