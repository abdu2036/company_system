@extends('layouts.admin')
@section('title', 'سجل السجلات الصناعية للشركات')
@section('content')
<div class="container-fluid pt-4">
    <nav aria-label="breadcrumb">
   
    </nav>

    <div class="card text-left" style="direction: rtl;">
        <div class="card-header bg-dark text-white d-flex flex-wrap justify-content-between align-items-center" style="display: flex !important; flex-direction: row-reverse; gap: 15px;">
            
            <h3 class="card-title mb-0 float-left">
                <i class="fas fa-industry"></i> جدول السجلات الصناعية للشركات 🏭
            </h3>
            
            <div class="card-tools d-flex align-items-center" style="gap: 10px; margin-left: auto;">
                
                <form action="{{ route('industrial.index') }}" method="GET" class="form-inline d-inline-flex">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control text-left" placeholder="ابحث باسم الشركة أو رقم السجل..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default bg-info text-white border-0">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('industrial.index') }}" class="btn btn-sm btn-secondary mr-1" title="إلغاء الفلترة">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>

                <a href="{{ route('industrial.create') }}" class="btn btn-info btn-sm text-white">
                    <i class="fas fa-plus-circle"></i> إضافة سجل جديد
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success m-3 alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 text-center align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>#ID</th>
                            <th>اسم الشركة</th>
                            <th>رقم السجل الصناعي</th>
                            <th>تاريخ الإصدار</th>
                            <th>مدة الصلاحية</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الحالة</th>
                            <th>المرفق</th>
                            <th>العمليات الإدارية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registers as $register)
                            <tr>
                                <td><strong>{{ $register->id }}</strong></td>
                                <td>{{ optional($register->company)->name ?? '---' }}</td>
                                <td><span class="badge badge-secondary p-2">{{ $register->industrial_code }}</span></td>
                                <td>{{ $register->issue_date->format('Y-m-d') }}</td>
                                <td>{{ $register->duration }}</td>
                                <td>{{ $register->expiry_date->format('Y-m-d') }}</td>
                                <td>
                                    @if($register->expiry_date->isPast())
                                        <span class="badge badge-danger px-3 py-1"><i class="fas fa-exclamation-triangle"></i> منتهي</span>
                                    @else
                                        <span class="badge badge-success px-3 py-1"><i class="fas fa-check-circle"></i> ساري</span>
                                    @endif
                                </td>
                                <td>
                                    @if($register->attachment)
                                        <a href="{{ asset('storage/' . $register->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> عرض
                                        </a>
                                    @else
                                        <span class="text-muted small">لا يوجد</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Actions">
                                        <a href="{{ route('industrial.renew', $register->id) }}" class="btn btn-sm text-white mr-1" style="background-color: #6f42c1; border-color: #6f42c1;" title="تجديد الصلاحية">
                                            <i class="fas fa-sync-alt"></i> تجديد
                                        </a>

                                        <a href="{{ route('industrial.edit', $register->id) }}" class="btn btn-sm btn-warning text-dark mr-1" title="تعديل البيانات">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>

                                        <form action="{{ route('industrial.destroy', $register->id) }}" method="POST" class="d-inline delete-form m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger confirm-delete-btn" title="حذف السجل نهائياً">
                                                <i class="fas fa-trash-alt"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="70" class="mb-3 opacity-50" alt="No data">
                                    <p class="text-muted font-weight-bold">
                                        {{ request('search') ? 'لا توجد نتائج تطابق بحثك الحالي.' : 'لا توجد سجلات صناعية مضافة حالياً.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- تم الاحتفاظ فقط بكود التنبيه الحديث والمنسق --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // التقاط حدث الضغط على زر الحذف باستخدام JQuery لضمان الفاعلية الكاملة مع الـ event-bubbling
    $(document).on('click', '.confirm-delete-btn', function (e) {
        e.preventDefault(); // إيقاف الحدث الفوري فوراً
        
        let form = $(this).closest('.delete-form');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف السجل الصناعي والملف المرفق نهائياً!",
                icon: 'warning',
                iconColor: '#f8bb86', /* الدائرة البرتقالية المطابقة للصورة تماماً */
                showCancelButton: true,
                confirmButtonColor: '#dc3545', /* الأحمر للحذف */
                cancelButtonColor: '#007bff',  /* الأزرق للإلغاء */
                confirmButtonText: 'نعم، احذف الآن',
                cancelButtonText: 'إلغاء',
                reverseButtons: true /* توزيع الأزرار الصحيح أفقيًا */
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            // حل احتياطي بسيط إذا لم تُحمّل المكتبة لسبب تقني عابر
            if (confirm('هل أنت متأكد من حذف هذا السجل نهائياً؟')) {
                form.submit();
            }
        }
    });
});
</script>
@endsection