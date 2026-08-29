<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetClientsAction
{
    public function execute(?string $search = null, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        return Client::query()
            ->select([
                'id', 'user_id', 'first_name', 'last_name', 'profile_photo',
                'email', 'phone', 'division', 'district', 'status', 'role', 'created_at',
            ])
            ->parents()
            ->filterBySearch($search)
            ->filterByStatus($status)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStats(): array
    {
        $stats = Client::query()
            ->parents()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive')
            ->first();

        return [
            'totalClients' => (int) ($stats->total ?? 0),
            'activeClients' => (int) ($stats->active ?? 0),
            'inactiveClients' => (int) ($stats->inactive ?? 0),
        ];
    }
}
