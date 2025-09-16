<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// -----------------------------
// 1. Get parent client ID
// -----------------------------
if (! function_exists('owner_client_id')) {
    function owner_client_id(): ?int
    {
        $user = Auth::guard('client')->user();

        return $user?->parent_id ?? $user?->id;
    }
}

// -----------------------------
// 2. Generic helper for sequential IDs
// -----------------------------
if (! function_exists('generate_sequential_id')) {
    function generate_sequential_id(string $prefix, string $column, string $table, int $pad = 2): string
    {
        return DB::transaction(function () use ($prefix, $column, $table, $pad) {
            // Lock table row to avoid race conditions
            $last = DB::table($table)
                ->select($column)
                ->where($column, 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByRaw("CAST(SUBSTRING($column, ".(strlen($prefix)+1).") AS UNSIGNED) DESC")
                ->first();

            // Extract last number
            if ($last && preg_match('/^'.$prefix.'(\d+)$/', $last->{$column}, $matches)) {
                $lastNumber = (int) $matches[1];
            } else {
                $lastNumber = 0;
            }

            $nextNumber = $lastNumber + 1;

            return $prefix.str_pad($nextNumber, $pad, '0', STR_PAD_LEFT);
        });
    }
}

// -----------------------------
// 3. Generate client user ID
// -----------------------------
if (! function_exists('generate_client_user_id')) {
    function generate_client_user_id(): string
    {
        return generate_sequential_id('UID', 'user_id', 'clients', 2);
    }
}

// -----------------------------
// 4. Generate invoice number
// -----------------------------
if (! function_exists('generate_invoice_number')) {
    function generate_invoice_number(): string
    {
        return generate_sequential_id('INV', 'invoice_number', 'invoices', 3);
    }
}

// -----------------------------
// 5. Generate payment ID
// -----------------------------
if (! function_exists('generate_payment_id')) {
    function generate_payment_id(): string
    {
        return generate_sequential_id('PAY', 'payment_id', 'payments', 3);
    }
}

// -----------------------------
// 6. Generate member ID
// -----------------------------
if (! function_exists('generate_member_id')) {
    function generate_member_id(): string
    {
        return generate_sequential_id('MID', 'member_id', 'members', 2);
    }
}

// -----------------------------
// 7. Authorize owner
// -----------------------------
if (! function_exists('authorize_owner')) {
    function authorize_owner(Model $model, string $column = 'client_id'): void
    {
        if ($model->{$column} !== owner_client_id()) {
            abort(403, 'You are not authorized to access this resource.');
        }
    }
}

// -----------------------------
// 8. Generate ticket number
// -----------------------------
if (! function_exists('generate_ticket_number')) {
    function generate_ticket_number(): string
    {
        return generate_sequential_id('TKT', 'ticket_number', 'tickets', 3);
    }
}
