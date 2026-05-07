<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->email;

        // 1. التحقق: هل الموظف موجود في قاعدة بيانات النظام الحالي؟
        $user = User::where('email', $email)->first();

        if (!$user) {
            // 2. البحث في قاعدة بيانات الـ HR
            $hrUser = DB::connection('mysql_hr')->table('users')->where('email', $email)->first();

            if ($hrUser) {
                // 3. استيراد الموظف تلقائياً
                $user = User::create([
                    'name' => $hrUser->name,
                    'email' => $hrUser->email,
                    'password' => $hrUser->password,
                ]);

                // إعطاؤه دور افتراضي لضمان دخوله
                $user->assignRole('employee'); 
            }
        }

        // إكمال عملية التحقق من كلمة المرور
        $request->authenticate();

        $request->session()->regenerate();

        // 4. جلب المستخدم بعد تسجيل الدخول لتحديد وجهته
        $loggedInUser = Auth::user();

        // --- منطق التوجيه الاحترافي باستخدام أسماء المسارات (Route Names) ---

        // المدير يوجه للتقارير
        if ($loggedInUser->hasRole('admin')) {
            return redirect()->route('reports.index');
        }

        // الفني يوجه لسجل الصيانة
        if ($loggedInUser->hasRole('technician')) {
            return redirect()->route('assets.maintenance_logs');
        }

        // مدخل البيانات يوجه لقائمة الأصول
        if ($loggedInUser->hasRole('asset_entry')) {
            return redirect()->route('assets.index');
        }

        // مدير المخزن يوجه للمخزن التالف
        if ($loggedInUser->hasRole('store_manager')) {
            return redirect()->route('assets.damaged');
        }

        // التوجه الافتراضي للموظف العادي لتجنب خطأ 404
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}