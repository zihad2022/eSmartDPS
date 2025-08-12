<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * All Admin Guard Permissions
         * Grouped logically for easier readability & maintenance.
         */
        $permissions = [
            // Roles
            'view roles', 'create roles', 'edit roles', 'delete roles',

            // Dashboard
            'view dashboard',

            // Clients
            'view clients', 'create clients', 'edit clients', 'delete clients',

            // Packages
            'view packages', 'create packages', 'edit packages', 'delete packages',

            // Invoices
            'view invoices', 'create invoices', 'edit invoices', 'delete invoices',

            // Tickets (Includes Ticket Chat)
            'view tickets', 'create tickets', 'edit tickets', 'delete tickets',
            'view ticket chats', 'send ticket messages',

            // Users (Admins)
            'view users', 'create users', 'edit users', 'delete users',
            'view user activities', // Moved from "Data Exports" to Users group

            // Settings
            'view settings', 'edit settings',

            // Data Exports
            'export clients', 'export packages', 'export invoices',

            // Profile
            'view profile', 'edit profile',
        ];

        // Create permissions for admin guard
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }

        /**
         * Create roles for admin guard
         */
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'admin']);
        $admin = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager',     'guard_name' => 'admin']);

        /**
         * Assign permissions
         */

        // Super Admin: All permissions
        $superAdmin->syncPermissions(Permission::where('guard_name', 'admin')->get());

        // Admin: All except certain sensitive permissions
        $admin->syncPermissions(
            Permission::where('guard_name', 'admin')
                ->whereNotIn('name', [
                    'delete roles',  // Can't delete roles
                    'edit settings', // Can't change settings
                    'export clients',
                    'export packages',
                    'export invoices',
                ])
                ->get()
        );

        // Manager: Limited operational permissions
        $manager->syncPermissions([
            'view dashboard',

            'view clients', 'create clients', 'edit clients',
            'view packages', 'create packages', 'edit packages',
            'view invoices', 'create invoices', 'edit invoices',

            // Tickets group (includes ticket chats)
            'view tickets', 'create tickets', 'edit tickets',
            'view ticket chats', 'send ticket messages',

            // Users group
            'view users',
            'view user activities',

            // Profile
            'view profile', 'edit profile',
        ]);
    }
}
