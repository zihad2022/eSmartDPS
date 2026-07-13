<?php

namespace App\Actions\Admin\Activities;

use App\Models\Activity;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Collection;

class GetRecentAdminActivitiesAction
{
    /**
     * @return Collection<int, Activity>
     */
    public function execute(int $limit = 5): Collection
    {
        return Activity::query()
            ->with('causer')
            ->forCauserType(Admin::class)
            ->latest('id')
            ->limit(max(1, min($limit, 20)))
            ->get();
    }
}
