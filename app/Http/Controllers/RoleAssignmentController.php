<?php

namespace App\Http\Controllers;

use App\Models\User; // القادم من mysql_hrms
use Spatie\Permission\Models\Role; // القادم من mysql (الشركات)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Spatie\Permission\Models\Permission;


class RoleAssignmentController extends Controller
{
public function index()
{
    // 1. جلب كل المستخدمين من HRMS
    $users = \App\Models\User::all();

    // 2. التصحيح: استخدم get() بدلاً من all() عند استخدام on()
    $roles = \Spatie\Permission\Models\Role::on('mysql')->get(); 

    // 3. ربط الأدوار المحقونة بكل موظف للعرض
 // داخل دالة index في RoleAssignmentController
foreach ($users as $user) {
    // 1. ابحث عن الموظف في قاعدة بيانات "الشركات" باستخدام بريده الإلكتروني
    $localUserInCompanyDB = \Illuminate\Support\Facades\DB::connection('mysql')
        ->table('users')
        ->where('email', $user->email)
        ->first();

    if ($localUserInCompanyDB) {
        // 2. استخدم الـ ID الخاص به في قاعدة الشركات لجلب الدور المحقون
        $roleId = \Illuminate\Support\Facades\DB::connection('mysql')
            ->table('model_has_roles')
            ->where('model_id', $localUserInCompanyDB->id) // هنا نستخدم الـ ID الصحيح (مثل 2 لجمال)
            ->value('role_id');

        if ($roleId) {
            // 3. جلب اسم الدور وتخزينه في كائن المستخدم ليعرض في الصفحة
            $user->current_role = \Illuminate\Support\Facades\DB::connection('mysql')
                ->table('roles')
                ->where('id', $roleId)
                ->value('name');
        }
    }
}

    return view('roles.index', compact('users', 'roles'));
}
public function update(Request $request, $userId)
{
    $request->validate(['role' => 'required']);

    // 1. جلب بيانات الموظف من HRMS
    $hrUser = \App\Models\User::find($userId); 
    if (!$hrUser) return redirect()->back()->with('error', 'الموظف غير موجود في نظام الموارد البشرية');

    // 2. البحث عن الموظف في قاعدة الشركات (mysql)
    $localUser = \Illuminate\Support\Facades\DB::connection('mysql')
        ->table('users')
        ->where('email', $hrUser->email)
        ->first();

    if (!$localUser) {
        // إنشاء المستخدم تلقائياً إذا لم يكن موجوداً (حل مشكلة عدم الاستيراد)
        $localUserId = \Illuminate\Support\Facades\DB::connection('mysql')
            ->table('users')
            ->insertGetId([
                'name'       => $hrUser->name,
                'email'      => $hrUser->email,
                'password'   => $hrUser->password, 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    } else {
        $localUserId = $localUser->id;
    }

    // جلب بيانات الدور من قاعدة الشركات
    $role = \Spatie\Permission\Models\Role::on('mysql')->where('name', $request->role)->first();
    if (!$role) return redirect()->back()->with('error', 'الدور المختار غير موجود في نظام الشركات');

    // 3. تحديث الدور باستخدام الـ ID الصحيح
    \Illuminate\Support\Facades\DB::connection('mysql')->transaction(function () use ($localUserId, $role) {
        // حذف الأدوار القديمة
        \Illuminate\Support\Facades\DB::connection('mysql')
            ->table('model_has_roles')
            ->where('model_id', $localUserId)
            ->where('model_type', 'App\Models\User')
            ->delete();

        // إسناد الدور الجديد
        \Illuminate\Support\Facades\DB::connection('mysql')
            ->table('model_has_roles')
            ->insert([
                'role_id'    => $role->id,
                'model_type' => 'App\Models\User',
                'model_id'   => $localUserId,
            ]);
    });

    // مسح الكاش ضروري لتحديث السايد بار فوراً
    \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');

    return redirect()->back()->with('success', 'تم مزامنة بيانات الموظف ومنحه دور: ' . $request->role);
}
// ... الكود العلوي كما هو (index, update)

public function store(Request $request)
{
    // هذه الدالة لاستيراد موظف من HRMS وإعطاؤه دور
    $employee = DB::connection('mysql_hrms')
                ->table('users')
                ->where('email', $request->email)
                ->first();

    if (!$employee) {
        return back()->with('error', 'الموظف غير موجود في نظام HRMS.');
    }

    $user = \App\Models\User::updateOrCreate(
        ['email' => $employee->email],
        [
            'name'     => $employee->name,
            'password' => $employee->password,
        ]
    );

    $user->syncRoles($request->role);
    \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');

    return back()->with('success', 'تم استيراد الموظف وتعيين الدور بنجاح!');
}

public function storeRole(Request $request)
{
    // هذه الدالة لإنشاء مسمى وظيفي جديد فقط (مثل super-admin)
    $request->validate([
        'name' => 'required|string|unique:roles,name',
    ]);

    // ننشئ الدور في قاعدة "الشركات" مباشرة
    \Spatie\Permission\Models\Role::create([
        'name' => $request->name,
        'guard_name' => 'web'
    ]);

    \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');

    return back()->with('success', 'تم إضافة المسمى الوظيفي الجديد بنجاح!');
}
public function updatePermission(Request $request)
{
    try {
        // 1. جلب الدور مع تحديد الاتصال
        $role = \Spatie\Permission\Models\Role::on('mysql')->findOrFail($request->role_id);
        
        // 2. التصحيح هنا: استخدام firstOrCreate بدلاً من findOrCreate
        // firstOrCreate تعمل بشكل ممتاز مع Builder (المسترجع بواسطة on('mysql'))
        $permission = \Spatie\Permission\Models\Permission::on('mysql')->firstOrCreate([
            'name' => $request->permission,
            'guard_name' => 'web' // تأكد من مطابقة الـ guard المستخدم في مشروعك
        ]);

        // 3. تحديث الصلاحية
        if ($request->status == 'true' || $request->status == true) {
            $role->givePermissionTo($permission);
        } else {
            $role->revokePermissionTo($permission);
        }

        return response()->json(['success' => true, 'message' => 'تم التحديث بنجاح']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
}