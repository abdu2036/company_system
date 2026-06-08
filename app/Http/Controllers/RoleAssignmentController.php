<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Spatie\Permission\Models\Role; 
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Lang;

class RoleAssignmentController extends Controller
{
    /**
     * 1. دالة العرض الرئيسية (تجمع بين الأدوار الحالية وموظفي نظام الـ HR حياً)
     */
    public function index()
    {
        // جلب الموظفين القادمين من قاعدة بيانات منظومة الموظفين (الاتصال الافتراضي للمشروع)
        $users = \App\Models\User::all();

        // جلب الأدوار مع صلاحياتها المسجلة على قاعدة بيانات الشركات (اتصال mysql المعين لـ Spatie)
        $roles = \Spatie\Permission\Models\Role::on('mysql')->with('permissions')->get(); 

        // تتبع الموظفين ومعرفة أدوارهم النشطة حالياً في نظام الشركات
        foreach ($users as $user) {
            // البحث عن الموظف في جدول مستخدمي الشركات باستخدام البريد الإلكتروني كحقل مشترك
            $localUserInCompanyDB = DB::connection('mysql')
                ->table('users')
                ->where('email', $user->email)
                ->first();

            // قيمة افتراضية في حال لم يملك دوراً بعد
            $user->current_role = null;

            if ($localUserInCompanyDB) {
                // جلب الـ role_id المرتبط بالمستخدم من جدول علاقات Spatie
                $roleId = DB::connection('mysql')
                    ->table('model_has_roles')
                    ->where('model_id', $localUserInCompanyDB->id) 
                    ->where('model_type', 'App\Models\User') // تحديد الـ model_type للحزمة
                    ->value('role_id');

                if ($roleId) {
                    // جلب اسم المسمى الوظيفي للدور وتخزينه لعرضه بالجدول
                    $user->current_role = DB::connection('mysql')
                        ->table('roles')
                        ->where('id', $roleId)
                        ->value('name');
                }
            }
        }

        return view('roles.index', compact('users', 'roles'));
    }

    /**
     * 2. دالة إسناد وتغيير الأدوار للموظفين القادمين من منظومة الـ HR
     */
    public function update(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        // جلب بيانات الموظف من منظومة الموظفين الحالية (الاتصال الأساسي) باستخدام الـ ID الممرر
        $hrUser = \App\Models\User::findOrFail($userId);

        // التحقق من وجود حساب مطابِق للموظف داخل قاعدة بيانات الشركات (اتصال mysql) باستخدام الإيميل
        $localUser = DB::connection('mysql')
            ->table('users')
            ->where('email', $hrUser->email)
            ->first();

        // إذا كان الموظف لم يسبق له دخول نظام الشركات، ننشئ له سجلاً تلقائياً لمطابقته ومنحه الصلاحيات
        if (!$localUser) {
            $localUserId = DB::connection('mysql')
                ->table('users')
                ->insertGetId([
                    'name'       => $hrUser->name,
                    'email'      => $hrUser->email,
                    'password'   => $hrUser->password ?? bcrypt('12345678'), // استخدام نفس كلمة المرور المشفرة أو افتراضية
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        } else {
            $localUserId = $localUser->id;
        }

        // التحقق من أن الدور المختار موجود وصالح في نظام الشركات
        $role = \Spatie\Permission\Models\Role::on('mysql')->where('name', $request->role)->first();
        
        if (!$role) {
            return redirect()->back()->with('error', 'الدور المختار غير معرف في نظام الشركات!');
        }

        // تحديث وإسناد الدور الجديد داخل جدول model_has_roles التابع للشركات
        DB::connection('mysql')->transaction(function () use ($localUserId, $role) {
            // تنظيف الأدوار السابقة الممنوحة للمستخدم لضمان تثبيت الدور الجديد فقط
            DB::connection('mysql')
                ->table('model_has_roles')
                ->where('model_id', $localUserId)
                ->where('model_type', 'App\Models\User')
                ->delete();

            // إدراج العلاقة الجديدة للدور
            DB::connection('mysql')
                ->table('model_has_roles')
                ->insert([
                    'role_id'    => $role->id,
                    'model_type' => 'App\Models\User',
                    'model_id'   => $localUserId,
                ]);
        });

        // تصفير كاش حزمة الصلاحيات ليتم تطبيق الدور للموظف فوراً
        Artisan::call('permission:cache-reset');

        return redirect()->route('roles.index')->with('success', 'تم تعيين المسمى الوظيفي للموظف وتحديث بيانات الربط بنجاح!');
    }

    // =========================================================================
    // الدوال المدمجة لإدارة الأدوار والصلاحيات نفسها (إضافة، تعديل، حذف الأدوار)
    // =========================================================================

    /**
     * صفحة إضافة دور جديد
     */
    public function createRole()
    {
        $permissions = Permission::on('mysql')->get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * حفظ الدور الجديد وصلاحياته في قاعدة البيانات
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:mysql.roles,name',
            'permissions' => 'required|array'
        ]);

        // إنشاء سجل الدور على اتصال الشركات
        $role = Role::on('mysql')->create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        // منح الصلاحيات المحددة
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        Artisan::call('permission:cache-reset');

        return redirect()->route('roles.index')->with('success', 'تم إضافة المسمى الوظيفي الجديد بنجاح!');
    }

    /**
     * 🛠️ تم التعديل هنا: صفحة تعديل الدور المنسقة والمجمعة تلقائياً
     */
    public function editRole($id)
    {
        // 1. جلب الدور من قاعدة بيانات الشركات
        $role = Role::on('mysql')->findOrFail($id);
        
        // 2. جلب كافة الصلاحيات من اتصال الـ mysql وتجميعها بناءً على أول مقطع قبل النقطة لملف الـ Blade الجديد
        $permissionsGrouped = Permission::on('mysql')->get()->groupBy(function($permission) {
            return explode('.', $permission->name)[0]; 
        });

        // 3. جلب مصفوفة بأسماء الصلاحيات الحالية التي يمتلكها الدور لمطابقتها في مربعات الاختيار (Checkboxes)
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        // 4. تمرير المتغير الجديد $permissionsGrouped لحل مشكلة الـ Undefined variable نهائياً
        return view('roles.edit', compact('role', 'permissionsGrouped', 'rolePermissions'));
    }

    /**
     * تحديث الدور والصلاحيات الممنوحة له
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::on('mysql')->findOrFail($id);

        // حماية دور المدير العام من التعديل العشوائي
        if ($role->name === 'admin' || $role->name === 'super-admin') {
            return redirect()->route('roles.index')->with('error', 'لا يمكن تعديل صلاحيات المدير العام الأساسي!');
        }

        $request->validate([
            'name' => 'required|string|unique:mysql.roles,name,' . $id,
            'permissions' => 'required|array'
        ]);

        $role->update(['name' => $request->name]);
        
        // إعادة مزامنة الصلاحيات الجديدة المحددة بالكامل
        $role->syncPermissions($request->permissions);

        Artisan::call('permission:cache-reset');

        return redirect()->route('roles.index')->with('success', 'تم تحديث بيانات الدور والصلاحيات بنجاح!');
    }

    /**
     * حذف الدور بالكامل
     */
    public function destroyRole($id)
    {
        $role = Role::on('mysql')->findOrFail($id);

        // منع حذف دور الأدمن العام مطلقاً
        if ($role->name === 'admin' || $role->name === 'super-admin') {
            return redirect()->route('roles.index')->with('error', 'محظور! لا يمكن حذف دور المدير العام.');
        }

        $role->delete();

        Artisan::call('permission:cache-reset');

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح.');
    }

    // ==========================================
    // دوال إضافية احتياطية
    // ==========================================
    public function store(Request $request) { 
        return $this->storeRole($request);
    }
    
    public function updatePermission(Request $request) {
        // إذا كنت تستخدم أجاكس لتعديل صلاحية معينة بشكل منفصل
    }
}