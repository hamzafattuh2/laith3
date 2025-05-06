<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
     public function handle(Request $request, Closure $next): Response
    {
        // 1. الحصول على بيانات المستخدم الحالي
        $user = auth()->user();
      //  $user = $request->user();

        /**
         * 2. التحقق من الشروط:
         * - هل المستخدم مسجل الدخول؟ (auth()->check())
         * - هل يوجد كود تحقق مخزن للمستخدم؟ ($user->code)
         */
        if (auth()->check() && $user->code) {
            
            $user->resetCode();
            /**
             * 3. التحقق من أن الطلب الحالي ليس لمسارات التحقق (verify*)
             * !$request->is('verify*') تعني: إذا لم يكن المسار يبدأ بـ verify
             */
            if (!$request->is('verify*')) {
                // توجيه المستخدم إلى صفحة التحقق
                return redirect()->route('verify.index');
            }
        }

        // 4. إذا لم يتم استيفاء الشروط، استمر في تنفيذ الطلب
        return $next($request);
    }
}
