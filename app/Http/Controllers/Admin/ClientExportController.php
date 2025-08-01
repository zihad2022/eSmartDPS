<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $fileName = 'clients_'.now()->format('Ymd_His').'.csv';

        $clients = Client::with('subscription')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($clients) {
            $file = fopen('php://output', 'w');

            // CSV Header Row
            fputcsv($file, [
                'sl',
                'User ID',
                'First Name',
                'Last Name',
                'Email',
                'Phone Number',
                'Division',
                'District',
                'Address',
                'Postal Code',
                'Role',
                'Status',
                'Subscription Plan',
                'Created At',
            ]);
            $sl = 1;
            foreach ($clients as $client) {
                fputcsv($file, [
                    '#'.$sl++,
                    $client->user_id,
                    $client->first_name,
                    $client->last_name,
                    $client->email,
                    $client->phone_number,
                    $client->division,
                    $client->district,
                    $client->address,
                    $client->postal_code,
                    $client->role,
                    $client->status ? 'Active' : 'Inactive',
                    $client->subscription->name ?? 'N/A',
                    $client->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
