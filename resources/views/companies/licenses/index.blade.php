@extends('layouts.admin')

@section('title', 'إدارة التراخيص')

@section('content_header')



@stop

@section('content')


{{-- عرض رسائل النجاح --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-right" role="alert" style="direction: rtl;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow-sm mb-3">
    <div class="card-body p-3">
        <div class="row align-items-center" style="direction: rtl;">
            <div class="col-md-3 mb-2 mb-md-0 text-right">
                <a href="{{ url('/licenses/create') }}" class="btn btn-primary btn-block shadow-sm">
                    <i class="fas fa-plus-circle"></i> إضافة ترخيص جديد
                </a>
            </div>

            <div class="col-md-9">
                <form action="{{ route('licenses.index') }}" method="GET" class="m-0">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="ابحث برقم الترخيص أو اسم الشركة..." 
                               value="{{ request('search') }}" 
                               style="border-radius: 0 5px 5px 0;">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-info" style="border-radius: 5px 0 0 5px;">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-info">
    <div class="card-header bg-info text-white text-right">
        <h3 class="card-title float-right font-weight-bold">قائمة التراخيص المسجلة للنظام 📋</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover text-center" style="direction: rtl;">
            <thead class="bg-light">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>اسم الشركة 🏢</th>
                    <th>رقم الترخيص 🔢</th>
                    <th>تاريخ الإصدار 📅</th>
                    <th>تاريخ الانتهاء ⏳</th>
                    <th>المرفق 📁</th>
                    <th>العمليات ⚙️</th>
                </tr>
            </thead>
            <tbody>
                @forelse($licenses as $license)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-weight-bold text-primary">{{ $license->company->name ?? 'غير محدد' }}</td>
                        <td><span class="badge badge-light border">{{ $license->license_number }}</span></td>
                        <td>{{ $license->issue_date }}</td>
                        <td>
                            {{-- منطق حساب الحالة وتلوينها مثل الغرفة التجارية --}}
                            @php
                                $expiryDate = \Carbon\Carbon::parse($license->expiry_date);
                                $today = \Carbon\Carbon::today();
                                $isExpiringSoon = $expiryDate->isAfter($today) && $expiryDate->diffInDays($today) <= 30;
                                $isExpired = $expiryDate->isBefore($today);
                            @endphp

                            @if($isExpired)
                                <span class="badge badge-danger p-2 shadow-sm w-100">
                                    <i class="fas fa-exclamation-triangle"></i> منتهي ({{ $license->expiry_date }})
                                </span>
                            @elseif($isExpiringSoon)
                                <span class="badge badge-warning p-2 shadow-sm w-100">
                                    <i class="fas fa-clock"></i> ينتهي قريباً ({{ $license->expiry_date }})
                                </span>
                            @else
                                <span class="badge badge-success p-2 shadow-sm w-100">
                                    <i class="fas fa-check-circle"></i> ساري ({{ $license->expiry_date }})
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($license->document_path)
                                <a href="{{ asset($license->document_path) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info rounded-pill px-3">
                                    <i class="fas fa-file-pdf text-danger"></i> عرض
                                </a>
                            @else
                                <span class="text-muted small">لا يوجد ❌</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">


                                {{-- زر التعديل باللون الأزرق --}}
                                <a href="{{ route('licenses.edit', $license->id) }}"
                                    class="btn btn-sm btn-info text-white mr-1 shadow-sm mx-1" style="width: 35px;"
                                    title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- زر التجديد باللون الأصفر --}}
                                <a href="{{ route('licenses.renew', $license->id) }}"
                                   class="btn btn-sm btn-success text-white mx-1" style="width: 35px;"
                                    title="تجديد">
                                    <i class="fas fa-sync-alt"></i>
                                </a>


                                {{-- زر الحذف باللون الأحمر --}}
                                <form action="{{ route('licenses.destroy', $license->id) }}" method="POST"
                                    id="delete-form-license-{{ $license->id }}" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger shadow-sm" style="width: 35px;"
                                        onclick="confirmDeleteLicense('{{ $license->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">لا توجد تراخيص مسجلة حالياً 📭</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<style>
    /* تحسينات التصميم لتكون مطابقة للغرفة التجارية */
    .table th,
    .table td {
        vertical-align: middle !important;
    }

    .badge {
        font-size: 0.85rem;
        border-radius: 5px;
    }

    .btn-sm {
        border-radius: 5px;
    }

    /* تأثير عند تمرير الماوس على الصفوف */
    .table-hover tbody tr:hover {
        background-color: rgba(23, 162, 184, 0.05);
    }
</style>
@stop
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // دالة حذف الغرفة التجارية
    function confirmDeleteChamber(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف السجل التجاري والمرفق نهائياً!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // أحمر
            cancelButtonColor: '#007bff',  // أزرق
            confirmButtonText: 'نعم، احذف الآن',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-chamber-' + id).submit();
            }
        });
    }

    // دالة حذف الترخيص
    function confirmDeleteLicense(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف الترخيص والمرفق نهائياً!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#007bff',
            confirmButtonText: 'نعم، احذف الآن',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-license-' + id).submit();
            }
        });
    }
</script>
@stop