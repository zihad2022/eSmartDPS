<?php

namespace App\Actions\Admin\Users;

use App\Models\Admin;

class GetAdminsAction
{
    public function __construct(
        private readonly GuardAdminAccountManagementAction $guardManagement,
    ) {}

    public function execute(Admin $actor, ?string $search, ?string $status, int $perPage = 10): array
    {
        $users = Admin::query()
            ->with('roles.permissions')
            ->when(filled($search), function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->active())
            ->when($status === 'inactive', fn ($query) => $query->inactive())
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $manageableUserIds = $users->getCollection()
            ->filter(fn (Admin $target): bool => $this->guardManagement->allows($actor, $target))
            ->pluck('id')
            ->all();

        $counts = Admin::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'users' => $users,
            'manageableUserIds' => $manageableUserIds,
            'totalUsers' => (int) $counts->sum(),
            'activeUsers' => (int) ($counts[1] ?? 0),
            'inactiveUsers' => (int) ($counts[0] ?? 0),
            'search' => $search,
        ];
    }
}
