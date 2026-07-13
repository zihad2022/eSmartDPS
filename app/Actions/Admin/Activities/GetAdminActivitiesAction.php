<?php

namespace App\Actions\Admin\Activities;

use App\Models\Activity;
use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAdminActivitiesAction
{
    public function execute(int $perPage = 10): LengthAwarePaginator
    {
        return Activity::query()
            ->with('causer')
            ->forCauserType(Admin::class)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
