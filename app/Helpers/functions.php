<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Get parent client id
if (! function_exists('owner_client_id')) {
    function owner_client_id(): ?int
    {
        $user = Auth::guard('client')->user();

        return $user?->parent_id ?? $user?->id;
    }
}

// Generate client user id
if (! function_exists('generate_client_user_id')) {
    function generate_client_user_id(): string
    {
        return DB::transaction(function () {
            $latest = Client::lockForUpdate()->orderByDesc('id')->first();

            if (! $latest || ! preg_match('/^UID(\d+)$/', $latest->user_id, $matches)) {
                return 'UID01';
            }

            $next = (int) $matches[1] + 1;

            return 'UID'.str_pad($next, 2, '0', STR_PAD_LEFT);
        });
    }
}

// Generate invoice number
if (! function_exists('generate_invoice_number')) {
    function generate_invoice_number(): string
    {
        return DB::transaction(function () {
            $lastInvoice = Invoice::where('invoice_number', 'like', 'INV%')
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING(invoice_number, 4) AS UNSIGNED) DESC')
                ->first();

            if ($lastInvoice && preg_match('/INV(\d+)/', $lastInvoice->invoice_number, $matches)) {
                $lastNumber = (int) $matches[1];
            } else {
                $lastNumber = 0;
            }

            $nextNumber = $lastNumber + 1;

            return 'INV'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }
}

// Generate payment id
if (! function_exists('generate_payment_id')) {
    function generate_payment_id(): string
    {
        return DB::transaction(function () {
            $lastPayment = Payment::where('payment_id', 'like', 'PAY%')
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING(payment_id, 4) AS UNSIGNED) DESC')
                ->first();

            if ($lastPayment && preg_match('/PAY(\d+)/', $lastPayment->payment_id, $matches)) {
                $lastNumber = (int) $matches[1];
            } else {
                $lastNumber = 0;
            }

            $nextNumber = $lastNumber + 1;

            return 'PAY'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }
}

// Generate member id
if (! function_exists('generate_member_id')) {
    function generate_member_id(): string
    {
        return DB::transaction(function () {
            $latest = Member::lockForUpdate()->orderByDesc('id')->first();

            if (! $latest || ! preg_match('/^MID(\d+)$/', $latest->member_id, $matches)) {
                return 'MID01';
            }

            $next = (int) $matches[1] + 1;

            return 'MID'.str_pad($next, 2, '0', STR_PAD_LEFT);
        });
    }
}

// Authorize owner
if (! function_exists('authorize_owner')) {
    function authorize_owner(Model $model, string $column = 'client_id'): void
    {
        if ($model->{$column} !== owner_client_id()) {
            abort(403, 'You are not authorized to access this resource.');
        }
    }
}
