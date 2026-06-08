<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // إعادة ضبط كاش الصلاحيات
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | الصلاحيات
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // الموظفين
            'employees.view', // عرض الموظفين
            'employees.create', // إضافة موظف جديد
            'employees.edit', // تعديل بيانات موظف
            'employees.delete', // حذف موظف
            'employees.print', // طباعة بيانات موظف
            'employees.export', // تصدير بيانات موظف
            'employees.approve', // الموافقة على بيانات موظف

            // الشركات
            'companies.view', // عرض الشركات
            'companies.create', // إضافة شركة جديدة
            'companies.edit', // تعديل بيانات شركة
            'companies.delete', // حذف شركة

            // الفروع
            'branches.view', // عرض الفروع
            'branches.create', // إضافة فرع جديد
            'branches.edit', // تعديل بيانات فرع
            'branches.delete', // حذف فرع

            // المخازن
            'warehouses.view', // عرض المخازن
            'warehouses.create', // إضافة مخزن جديد
            'warehouses.edit', // تعديل بيانات مخزن
            'warehouses.delete', // حذف مخزن
            'warehouses.transfer', // نقل البضائع بين المخازن

            // الرواتب
            'salaries.view', // عرض الرواتب
            'salaries.create', // إضافة راتب جديد
            'salaries.edit', // تعديل بيانات راتب
            'salaries.delete', // حذف راتب
            'salaries.approve',

            // الإيرادات
            'revenues.view', // عرض الإيرادات
            'revenues.create', // إضافة إيراد جديد
            'revenues.edit', // تعديل بيانات إيراد
            'revenues.delete', // حذف إيراد
            'revenues.approve', // الموافقة على إيراد

            // المصروفات
            'expenses.view', // عرض المصروفات
            'expenses.create', // إضافة مصروف جديد
            'expenses.edit', // تعديل بيانات مصروف
            'expenses.delete', // حذف مصروف
            'expenses.approve', // الموافقة على مصروف

            // التقارير العامة
            'reports.view', // عرض التقارير
            'reports.export', // تصدير التقارير
            'reports.print',// طباعة التقارير

            // التقارير المالية
            'financial_reports.view', // عرض التقارير المالية
            'financial_reports.export', // تصدير التقارير المالية
            'financial_reports.print', // طباعة التقارير المالية

            // المستخدمين
            'users.view', // عرض المستخدمين
            'users.create', // إضافة مستخدم جديد
            'users.edit', // تعديل بيانات مستخدم
            'users.delete', // حذف مستخدم

            // الأدوار
            'roles.view', // عرض الأدوار
            'roles.create', // إضافة دور جديد
            'roles.edit', // تعديل بيانات دور
            'roles.delete', // حذف دور

            // الصلاحيات
            'permissions.view', // عرض الصلاحيات
            'permissions.create', // إضافة صلاحية جديدة
            'permissions.edit', // تعديل بيانات صلاحية
            'permissions.delete', // حذف صلاحية

            // الإعدادات
            'settings.manage', // إدارة الإعدادات
        ];

        /*
        |--------------------------------------------------------------------------
        | إنشاء الصلاحيات
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | الأدوار (Roles)
        |--------------------------------------------------------------------------
        */

        // Super Admin - كامل الصلاحيات
        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);

        $superAdmin->syncPermissions(Permission::all());

        // Admin - مدير النظام
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $admin->syncPermissions(
            Permission::whereNotIn('name', [
                'permissions.delete',
                'roles.delete'
            ])->pluck('name')->toArray()
        );

        // Accountant - المحاسب
        $accountant = Role::firstOrCreate([
            'name' => 'accountant',
            'guard_name' => 'web'
        ]);

        $accountant->syncPermissions([

            // الموظفين
            'employees.view',

            // الشركات والفروع
            'companies.view',
            'branches.view',

            // المخازن
            'warehouses.view',
            'warehouses.transfer',

            // الرواتب
            'salaries.view',
            'salaries.create',
            'salaries.edit',
            'salaries.approve',

            // الإيرادات
            'revenues.view',
            'revenues.create',
            'revenues.edit',
            'revenues.approve',

            // المصروفات
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.approve',

            // التقارير
            'reports.view',
            'reports.export',
            'reports.print',

            // التقارير المالية
            'financial_reports.view',
            'financial_reports.export',
            'financial_reports.print',
        ]);

        // مدير فرع
        $branchManager = Role::firstOrCreate([
            'name' => 'branch-manager',
            'guard_name' => 'web'
        ]);

        $branchManager->syncPermissions([
            'employees.view',
            'employees.create',
            'employees.edit',

            'branches.view',

            'warehouses.view',
            'warehouses.transfer',

            'reports.view',
            'reports.print',
        ]);

        // مدخل بيانات
        $dataEntry = Role::firstOrCreate([
            'name' => 'data-entry',
            'guard_name' => 'web'
        ]);

        $dataEntry->syncPermissions([
            'employees.view',
            'employees.create',
            'employees.edit',

            'revenues.view',
            'revenues.create',

            'expenses.view',
            'expenses.create',
        ]);

        /*
        |--------------------------------------------------------------------------
        | إنشاء مستخدم Super Admin تلقائياً
        |--------------------------------------------------------------------------
        */

        $user = User::firstOrCreate(
            [
                'email' => 'admin@system.com'
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
            ]
        );

        if (!$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }
    }
}