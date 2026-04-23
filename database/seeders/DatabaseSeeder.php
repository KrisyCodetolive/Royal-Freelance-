<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create permissions
        $this->createPermissions();

        // Create roles
        $this->createRoles();

        // Create default tenant
        $tenant = $this->createDefaultTenant();

        // Create super admin user
        $this->createSuperAdmin($tenant);
        
    }

    protected function createPermissions(): void
    {
        $permissions = [
            // Tenant management
            'view_tenants',
            'create_tenants',
            'update_tenants',
            'delete_tenants',

            // User management
            'view_users',
            'create_users',
            'update_users',
            'delete_users',

            // Offer management
            'view_offers',
            'create_offers',
            'update_offers',
            'delete_offers',

            // Funnel management
            'view_funnels',
            'create_funnels',
            'update_funnels',
            'delete_funnels',
            'duplicate_funnels',
            'publish_funnels',

            // Page management
            'view_pages',
            'create_pages',
            'update_pages',
            'delete_pages',

            // Lead management
            'view_leads',
            'create_leads',
            'update_leads',
            'delete_leads',
            'export_leads',
            'import_leads',

            // Tag management
            'view_tags',
            'create_tags',
            'update_tags',
            'delete_tags',

            // Alert management
            'view_alerts',
            'manage_alerts',

            // Analytics
            'view_analytics',
            'view_global_analytics',

            // Settings
            'manage_settings',
            'manage_scoring_rules',
            'manage_templates',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    protected function createRoles(): void
    {
        // Super Admin - can do everything
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - can do everything except tenant management
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::where('name', 'not like', '%tenants%')->get());

        // Manager - can manage funnels, leads, and team
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'view_users',
            'create_users',
            'update_users',
            'view_offers',
            'create_offers',
            'update_offers',
            'view_funnels',
            'create_funnels',
            'update_funnels',
            'duplicate_funnels',
            'publish_funnels',
            'view_pages',
            'create_pages',
            'update_pages',
            'view_leads',
            'create_leads',
            'update_leads',
            'export_leads',
            'import_leads',
            'view_tags',
            'create_tags',
            'update_tags',
            'view_alerts',
            'manage_alerts',
            'view_analytics',
            'manage_templates',
        ]);

        // Commercial - can manage their assigned leads
        $commercial = Role::firstOrCreate(['name' => 'commercial', 'guard_name' => 'web']);
        $commercial->givePermissionTo([
            'view_funnels',
            'view_pages',
            'view_leads',
            'update_leads',
            'view_tags',
            'view_alerts',
            'view_analytics',
        ]);
    }

    protected function createDefaultTenant(): Tenant
    {
        return Tenant::firstOrCreate(
            ['slug' => 'royal'],
            [
                'name' => 'Royal',
                'email' => 'contact@royal.com',
                'settings' => [
                    'scoring_thresholds' => [
                        'cold' => 0,
                        'warm' => 11,
                        'hot' => 31,
                        'ultra_hot' => 61,
                    ],
                ],
                'branding' => [
                    'primary_color' => '#6366f1',
                    'secondary_color' => '#8b5cf6',
                    'accent_color' => '#f59e0b',
                ],
            ]
        );
    }

    protected function createSuperAdmin(Tenant $tenant): User
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@royal.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'tenant_id' => $tenant->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('super_admin');

        // Create default scoring rules for tenant
        \App\Models\ScoringRule::createDefaultsForTenant($tenant);

        return $user;
    }
}
