<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // 1. التقاط أخطاء نقص الصلاحيات والأدوار من حزمة Spatie (خطأ 403)
        $this->renderable(function (UnauthorizedException $e, $request) {
            return $this->handleAccessDenied($request, 'عذراً! لا تمتلك الصلاحيات الكافية للوصول إلى هذه الصفحة 🚫');
        });

        // 2. التقاط أخطاء منع الوصول العامة من الرابط مباشرة (خطأ 403)
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            return $this->handleAccessDenied($request, 'عذراً! هذا الإجراء غير مسموح لحسابك حالياً 🔐');
        });

        // 3. التقاط أخطاء الروابط والبيانات غير الموجودة (خطأ 404)
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'المصدر المطلوب غير موجود!'], 404);
            }

            return redirect()->route('roles.index')
                ->with('error', 'عذراً، الصفحة التي تحاول الوصول إليها غير موجودة أو تم نقلها! 🔍');
        });
    }

    /**
     * دالة مساعدة مخصصة لإرجاع المستخدم خطوة للخلف مع فلاش مسج
     */
    private function handleAccessDenied($request, $message)
    {
        // إذا كان الطلب قادم عبر API أو AJAX أرجع استجابة JSON بدلاً من التوجيه
        if ($request->expectsJson()) {
            return response()->json(['error' => 'غير مصرح لك بالوصول المباشر'], 403);
        }

        // جلب الرابط السابق الذي جاء منه المستخدم
        $previousUrl = url()->previous();
        // الرابط الاحتياطي في حال لم يملك تاريخ تصفح سابق لمنع التكرار اللانهائي
        $fallbackUrl = route('roles.index');

        // التأكد من أن الرابط السابق ليس هو الرابط الحالي لتجنب حلقة توجيه مغلقة
        $redirectUrl = ($previousUrl && $previousUrl !== url()->current()) ? $previousUrl : $fallbackUrl;

        return redirect()->to($redirectUrl)->with('error', $message);
    }
}