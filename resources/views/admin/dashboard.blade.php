@extends('layouts.admin') {{-- تأكد من اسم الـ layout المستخدم عندك --}}
@section('title', 'لوحة التحكم - نظام الشركات')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body text-center py-5">
                    <h2 class="text-primary">مرحباً بك، {{ auth()->user()->name }}</h2>
                    <p class="text-muted">أنت الآن في النظام الخاص بالشركات.</p>
                    <hr class="w-25 mx-auto">
                    <div class="mt-3">
                        @php
                            // 1. جلب اسم الدور الأول للمستخدم من نظام الصلاحيات
                            $roleName = auth()->user()->getRoleNames()->first();

                            // 2. مصفوفة الترخيص والترجمة الذكية للأدوار المتوفرة في نظامك
                            $rolesTranslation = [
                                'super_admin' => 'مدير نظام خارق',
                                'manager'     => 'المدير العام',
                                'accountant'  => 'المحاسب',
                                'employee'    => 'موظف',
                                'admin'       => 'مدير',
                            ];

                            // 3. التحقق إذا كان الدور موجوداً في المصفوفة لترجمته، وإلا يعرض الاسم الافتراضي
                            $translatedRole = $rolesTranslation[$roleName] ?? ($roleName ?? 'موظف');
                        @endphp

                        <span class="badge bg-info p-2 fs-6 fw-bold">الدور الحالي: {{ $translatedRole }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection