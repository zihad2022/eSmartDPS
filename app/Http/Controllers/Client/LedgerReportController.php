<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use Illuminate\Http\Request;

class LedgerReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
         // 1. Get filter values
         $fromDate = $request->input('from_date');
         $toDate = $request->input('to_date');
         $categoryId = $request->input('ledger_category_id');
 
         // 2. Build query
         $query = Ledger::query();
 
         if ($fromDate) {
             $query->whereDate('entry_date', '>=', $fromDate);
         }
         if ($toDate) {
             $query->whereDate('entry_date', '<=', $toDate);
         }
         if ($categoryId) {
             $query->where('ledger_category_id', $categoryId);
         }
 
         // 3. Fetch records
         $ledgers = $query->with('ledgerCategory')->orderBy('entry_date')->get();
 
         // 4. Calculate total
         $total = $ledgers->sum(function ($ledger) {
             return $ledger->type === 1   // suppose 1 = credit, 0 = debit
                 ? $ledger->amount
                 : -$ledger->amount;
         });
 
         // 5. Pass to view
         return view('client.ledger.report', [
             'ledgers' => $ledgers,
             'categories' => LedgerCategory::pluck('name', 'id'),
             'total' => $total,
         ]);
    }
}
