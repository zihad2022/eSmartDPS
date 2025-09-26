<?php

namespace App\Repositories\Admin;

use App\Models\Client;

class ClientRepository
{
    public function searchAndFilter(?string $search, ?string $status, int $perPage = 10)
    {
        return Client::select('id','user_id','first_name','last_name','profile_photo',
            'email','phone','division','district','status','role','created_at')
            ->parents()
            ->filterBySearch($search)
            ->filterByStatus($status)
            ->latest()
            ->paginate($perPage);
    }

    public function getStats(): array
    {
        return [
            'totalClients' => Client::parents()->count(),
            'activeClients' => Client::activeParents()->count(),
            'inactiveClients' => Client::inactiveParents()->count(),
        ];
    }
}