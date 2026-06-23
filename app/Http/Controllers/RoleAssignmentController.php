<?php

namespace App\Http\Controllers;

use App\Models\User; 
use App\Models\Employee; // استدعاء موديل الموظف المربوط بقاعدة الـ HRMS
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
        // جلب الموظفين القادمين من قاعدة بيانات منظومة الموظفين الذين لديهم بريد إلكتروني فقط
        $employees = \App\Models\Employee::whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        // جلب الأدوار مع صلاحياتها المسجلة على قاعدة بيانات الشركات (اتصال mysql المعين لـ Spatie)
        $roles = \Spatie\Permission\Models\Role::on('mysql')->with('permissions')->get(); 

        // تتبع الموظفين ومعرفة أدوارهم النشطة حالياً في نظام الشركات
        foreach ($employees as $employee) {
            // البحث عن الموظف في جدول مستخدمي الشركات باستخدام البريد الإلكتروني كحقل مشترك
            $localUserInCompanyDB = DB::connection('mysql')
                ->table('users')
                ->where('email', $employee->email)
                ->first();

            // قيمة افتراضية في حال لم يملك دوراً بعد
            $employee->current_role = null;

            if ($localUserInCompanyDB) {
                // جلب الـ role_id المرتبط بالمستخدم من جدول علاقات Spatie
                $roleId = DB::connection('mysql')
                    ->table('model_has_roles')
                    ->where('model_id', $localUserInCompanyDB->id) 
                    ->where('model_type', 'App\Models\User') 
                    ->value('role_id');

                if ($roleId) {
                    // جلب اسم المسمى الوظيفي للدور وتخزينه لعرضه بالجدول
                    $employee->current_role = DB::connection('mysql')
                        ->table('roles')
                        ->where('id', $roleId)
                        ->value('name');
                }
            }
        }

        return view('roles.index', compact('employees', 'roles'));
    }

    /**
     * 2. دالة إسناد وتغيير الأدوار للموظفين القادمين من منظومة الـ HR
     */
 /**
     * 2. دالة إسناد وتغيير الأدوار للموظفين القادمين من منظومة الـ HR
     */
    public function update(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        // 1. جلب بيانات الموظف من منظومة الموظفين الحالية (اتصال الـ HRMS تلقائياً عبر الموديل)
        $hrUser = \App\Models\Employee::findOrFail($userId);

        // تنظيف البريد الإلكتروني وتحويله لحروف صغيرة ليتطابق مع الـ phpMyAdmin تماماً
        $cleanEmail = strtolower(trim($hrUser->email));

        // 2. 🛠️ جلب اسم الاتصال (Connection) الذي يعتمد عليه موديل الـ HR ديناميكياً لضمان البحث في الداتابيز الصحيحة
        $hrConnection = $hrUser->getConnectionName() ?? config('database.default');

        // 3. جلب حساب المستخدم من جدول users التابع للـ HR باستخدام الاتصال الصحيح
        $hrUserAccount = DB::connection($hrConnection)
            ->table('users')
            ->where('email', $cleanEmail)
            ->first();

        // 4. خطوة احتياطية صارمة: إذا لم يجده بالاستعلام المباشر، نبحث عنه عبر موديل المستخدم الافتراضي
        if (!$hrUserAccount && class_exists('\App\Models\User')) {
            $hrUserAccount = \App\Models\User::where('email', $cleanEmail)->first();
        }

        // 5. التحقق من وجود حساب مطابِق للموظف داخل قاعدة بيانات الشركات (اتصال mysql)
        $localUser = DB::connection('mysql')
            ->table('users')
            ->where('email', $cleanEmail)
            ->first();

        // 6. التحقق النهائي والصارم من وجود الباسورد في الـ HR لمنع التمرير العشوائي
        if (!$hrUserAccount) {
            return redirect()->back()->with('error', 'خطأ: لم يتم العثور على حساب مستخدم للإيميل (' . $cleanEmail . ') داخل اتصال قاعدة الـ HR ('.$hrConnection.'). يرجى التأكد من تطابق الإيميل في جدول المستخدمين!');
        }

        // جلب كلمة المرور المشفرة الأصلية القادمة من الـ HR
        $hrPassword = $hrUserAccount->password;

        // 7. إذا كان الموظف لم يسبق له دخول نظام الشركات، ننشئ له سجلاً تلقائياً
        if (!$localUser) {
            $localUserId = DB::connection('mysql')
                ->table('users')
                ->insertGetId([
                    'name'        => $hrUser->full_name ?? $hrUser->name, 
                    'email'       => $cleanEmail,
                    'password'    => $hrPassword, // الباسورد الحقيقي المشفر
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
        } else {
            $localUserId = $localUser->id;

            // 🛠️ تحديث كلمة المرور في قاعدة الشركات لتصبح مطابقة تماماً وبقوة لباسورد الـ HR الحالي
            DB::connection('mysql')
                ->table('users')
                ->where('id', $localUserId)
                ->update([
                    'password'   => $hrPassword,
                    'updated_at' => now()
                ]);
        }

        // 8. التحقق من أن الدور المختار موجود وصالح في نظام الشركات
        $role = \Spatie\Permission\Models\Role::on('mysql')->where('name', $request->role)->first();
        
        if (!$role) {
            return redirect()->back()->with('error', 'الدور المختار غير معرف في نظام الشركات!');
        }

        // 9. تحديث وإسناد الدور الجديد داخل جدول model_has_roles التابع للشركات
        DB::connection('mysql')->transaction(function () use ($localUserId, $role) {
            DB::connection('mysql')
                ->table('model_has_roles')
                ->where('model_id', $localUserId)
                ->where('model_type', 'App\Models\User')
                ->delete();

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

        return redirect()->route('roles.index')->with('success', 'تم تعيين المسمى الوظيفي بنجاح، وتم جلب ومزامنة كلمة المرور الأصلية من منظومة الـ HR!');
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
     * صفحة تعديل الدور المنسقة والمجمعة تلقائياً
     */
    public function editRole($id)
    {
        $role = Role::on('mysql')->findOrFail($id);
        
        $permissionsGrouped = Permission::on('mysql')->get()->groupBy(function($permission) {
            return explode('.', $permission->name)[0]; 
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissionsGrouped', 'rolePermissions'));
    }

    /**
     * تحديث الدور والصلاحيات الممنوحة له
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::on('mysql')->findOrFail($id);

        if ($role->name === 'admin' || $role->name === 'super-admin') {
            return redirect()->route('roles.index')->with('error', 'لا يمكن تعديل صلاحيات المدير العام الأساسي!');
        }

        $request->validate([
            'name' => 'required|string|unique:mysql.roles,name,' . $id,
            'permissions' => 'required|array'
        ]);

        $role->update(['name' => $request->name]);
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

        if ($role->name === 'admin' || $role->name === 'super-admin') {
            return redirect()->route('roles.index')->with('error', 'محظور! لا يمكن حذف دور المدير العام.');
        }

        $role->delete();

        Artisan::call('permission:cache-reset');

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح.');
    }

    // ==========================================
    // دوال إضافية احتياطية (تم الاحتفاظ بها كاملة دون أي حذف)
    // ==========================================
    public function store(Request $request) { 
        return $this->storeRole($request);
    }
    
    public function updatePermission(Request $request) {
        // إذا كنت تستخدم أجاكس لتعديل صلاحية معينة بشكل منفصل
    }
}