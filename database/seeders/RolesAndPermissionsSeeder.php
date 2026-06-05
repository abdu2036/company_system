<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. إعادة ضبط كاش الصلاحيات (ضروري جداً لكي يشعر النظام بالتغييرات)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. قائمة بجميع الصلاحيات التي يطلبها الـ Blade في مشروعك
        $permissions = [
            'view assets',
            'edit assets',
            'delete assets',
            'send store',
            'manage maintenance',
            'view reports',
            'manage finance',
            'manage reports'
        ];

        // إنشاء الصلاحيات إذا لم تكن موجودة (استخدام guard_name يضمن التوافق)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 3. إنشاء أو جلب الأدوار (Roles) وربطها بالصلاحيات
        
        // دور الفني (technician)
      // الخطأ كان هنا: ['name' => 'technician','send store', ...]
$tech = Role::firstOrCreate(['name' => 'technician', 'guard_name' => 'web']);
$tech->syncPermissions(['view assets', 'manage maintenance', 'send store']); // أضف send store هنا

        // دور مدخل البيانات (asset_entry)
        $entry = Role::firstOrCreate(['name' => 'asset_entry', 'guard_name' => 'web']);
        $entry->syncPermissions(['view assets', 'edit assets']);

        // دور المدير (admin) - نعطيه كل الصلاحيات
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());
        
        // دور الـ Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());
    }
}