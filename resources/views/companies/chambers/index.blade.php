@extends('layouts.admin')

@section('title', 'اشتراكات الغرفة التجارية')


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
                 <a href="{{ route('chambers.create') }}"class="btn btn-info btn-block shadow-sm text-white font-weight-bold">
                    <i class="fas fa-plus-circle"></i> إضافة اشتراك جديد
                </a>
            </div>
            

            <div class="col-md-9">
                <form action="{{ route('chambers.index') }}" method="GET" class="m-0">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="ابحث برقم العضوية أو اسم الشركة..." 
                               value="{{ request('search') }}" 
                               style="border-radius: 0 5px 5px 0;">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary" style="border-radius: 5px 0 0 5px;">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<br>
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white text-right">
        <h3 class="card-title float-right">بيانات الغرفة التجارية المسجلة 📋</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover text-center" style="direction: rtl;">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>اسم الشركة 🏢</th>
                    <th>رقم العضوية 🔢</th>
                    <th>تاريخ الاشتراك 📅</th>
                    <th>تاريخ الانتهاء ⏳</th>
                    <th>المرفق 📁</th>
                    <th>العمليات ⚙️</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chambers as $chamber)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-weight-bold">{{ $chamber->company->name ?? 'غير محدد' }}</td>
                        <td>{{ $chamber->chamber_number }}</td>
                        <td>{{ $chamber->issue_date }}</td>
                        <td>
                            {{-- منطق حساب الحالة وتلوينها --}}
                            @php
                                $expiryDate = \Carbon\Carbon::parse($chamber->expiry_date);
                                $today = \Carbon\Carbon::today();
                                $isExpiringSoon = $expiryDate->isAfter($today) && $expiryDate->diffInDays($today) <= 30;
                                $isExpired = $expiryDate->isBefore($today);
                            @endphp

                            @if($isExpired)
                                <span class="badge badge-danger p-2 shadow-sm w-100">
                                    <i class="fas fa-exclamation-triangle"></i> منتهي ({{ $chamber->expiry_date }})
                                </span>
                            @elseif($isExpiringSoon)
                                <span class="badge badge-warning p-2 shadow-sm w-100">
                                    <i class="fas fa-clock"></i> ينتهي قريباً ({{ $chamber->expiry_date }})
                                </span>
                            @else
                                <span class="badge badge-success p-2 shadow-sm w-100">
                                    <i class="fas fa-check-circle"></i> ساري ({{ $chamber->expiry_date }})
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($chamber->document_path)
                                <a href="{{ asset($chamber->document_path) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-pdf"></i> عرض المرفق
                                </a>
                            @else
                                <span class="text-muted small">لا يوجد</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('chambers.edit', $chamber->id) }}"
                                    class="btn btn-sm btn-info text-white mx-1" style="width: 35px;" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{ route('chambers.renew', $chamber->id) }}"
                                    class="btn btn-sm btn-success text-white mx-1" style="width: 35px;" title="تجديد">
                                    <i class="fas fa-sync-alt"></i>
                                </a>



                                <form action="{{ route('chambers.destroy', $chamber->id) }}" method="POST"
                                    id="delete-form-chamber-{{ $chamber->id }}" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger shadow-sm" style="width: 35px;"
                                        onclick="confirmDeleteChamber('{{ $chamber->id }}')">
                                        <i class="fas fa-trash mx-1"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">لا توجد اشتراكات مسجلة حالياً 📭</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<style>
    .table th,
    .table td {
        vertical-align: middle !important;
    }

    .badge {
        font-size: 0.9rem;
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