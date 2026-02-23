<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Permission
        $permissions = [
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'supplier.view',
            'supplier.create',
            'supplier.edit',
            'supplier.delete',
            'patient.view',
            'patient.create',
            'patient.edit',
            'patient.delete',

            // Transaksi
            'purchase.view',
            'purchase.create',
            'purchase.edit',
            'purchase.delete',
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Role
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $staff = Role::firstOrCreate(['name' => 'apoteker']);
        $staff = Role::firstOrCreate(['name' => 'patient']);

        // Assign permission ke role
        $admin->syncPermissions($permissions);
        $staff->syncPermissions([
            'user.view'
        ]);
    }
}
