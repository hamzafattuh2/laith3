<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.verify');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     // الحصول على بيانات المستخدم الحالي المسجل الدخول
    //     $user = auth()->user();

    //     /**
    //      * التحقق من تطابق كود التحقق المدخل مع الكود المخزن في قاعدة البيانات
    //      *
    //      * 1. $request->input('code') - الحصول على كود التحقق من بيانات الطلب
    //      * 2. $user->code - الكود المخزن في سجل المستخدم
    //      */
    //     if ($request->input('code') == $user->code) {
    //         // إذا كان الكود صحيحاً:

    //         // 1. إعادة تعيين كود التحقق (مسحه بعد الاستخدام)
    //         $user->resetCode();

    //         // 2. توجيه المستخدم إلى لوحة التحكم
    //         return redirect()->route('dashboard');

    //         // ملاحظة: يمكن إضافة رسالة نجاح إذا أردت:
    //         // return redirect()->route('dashboard')->with('success', 'تم التحقق بنجاح');
    //     }

    //     // إذا كان الكود غير صحيح:
    //     // إعادة توجيه المستخدم للصفحة السابقة مع عرض رسالة الخطأ
    //     return redirect()->back()->withErrors(['code' => 'كود التحقق غير صحيح']);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4'
        ]);

        $user = auth()->user();

        if ($request->input('code') == $user->code) {
            $user->resetCode();

            // إرجاع رد مختلف لطلبات API
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'تم التحقق بنجاح',
                    'verified' => true
                ]);
            }

            return redirect()->route('dashboard');
        }

        // إرجاع رد مختلف لطلبات API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'كود التحقق غير صحيح',
                'verified' => false
            ], 422);
        }

        return redirect()->back()->withErrors(['code' => 'كود التحقق غير صحيح']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
