<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run()
{
    // 1. إعادة ضبط الكاش
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // 2. إنشاء الصلاحيات (Permissions)
    Permission::firstOrCreate(['name' => 'view assets']);
    Permission::firstOrCreate(['name' => 'manage reports']);
    Permission::firstOrCreate(['name' => 'manage finance']);

    // 3. ربط الصلاحيات بالأدوار الموجودة عندك في الصورة
    
    // دور الفني (technician)
    $tech = Role::where('name', 'technician')->first();
    $tech->givePermissionTo('view assets');

    // دور مدخل البيانات (asset_entry)
    $entry = Role::where('name', 'asset_entry')->first();
    $entry->givePermissionTo('view assets');

    // دور المدير (admin)
    $admin = Role::where('name', 'admin')->first();
    $admin->givePermissionTo(Permission::all());
}
}
