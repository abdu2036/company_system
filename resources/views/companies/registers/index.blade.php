@extends('layouts.admin')

@section('title', 'قائمة السجلات التجارية')

@section('content_header')
<h1 class="text-right">إدارة السجلات التجارية 📃</h1>
@stop

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('commercial-registers.index') }}" method="GET">
            <div class="row">
                <div class="col-md-10">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="ابحث برقم السجل، اسم المفوض، أو اسم الشركة..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-info">
        <h3 class="card-title float-right">بيانات السجلات المسجلة</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover text-right" style="direction: rtl;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الشركة 🏢</th>
                    <th>رقم السجل 🔢</th>
                    <th>المفوض 👤</th>
                    <th>تاريخ الانتهاء 📅</th>
                    <th>المرفق 📂</th>
                    <th>العمليات ⚙️</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registers as $register)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $register->company->name ?? 'غير محدد' }}</td>
                        <td>{{ $register->cr_number }}</td>
                        <td>{{ $register->representative_name }}</td>
                        <td>
                            @php
                                $expiryDate = \Carbon\Carbon::parse($register->expiry_date);
                                $now = \Carbon\Carbon::now();
                                $daysRemaining = $now->diffInDays($expiryDate, false);
                            @endphp

                            @if($daysRemaining < 0)
                                <span class="badge badge-danger p-2">
                                    <i class="fas fa-exclamation-circle"></i> منتهي ({{ $register->expiry_date }})
                                </span>
                            @elseif($daysRemaining <= 30)
                                <span class="badge badge-warning p-2">
                                    <i class="fas fa-clock"></i> ينتهي قريباً ({{ $register->expiry_date }})
                                </span>
                            @else
                                <span class="badge badge-success p-2">
                                    <i class="fas fa-check-circle"></i> ساري ({{ $register->expiry_date }})
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($register->document_path)
                                <a href="{{ asset($register->document_path) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary" title="عرض الملف">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @else
                                <span class="text-muted">لا يوجد</span>
                            @endif
                        </td>
                        <td>
                           

                            {{-- النموذج المخفي للحذف يجب أن يكون داخل الـ td ليرتبط بكل سجل --}}
                            <form id="delete-form-{{ $register->id }}"
                                action="{{ route('commercial-registers.destroy', $register->id) }}" method="POST"
                                style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <a href="{{ route('commercial-registers.edit', $register->id) }}" class="btn btn-sm btn-info"
                                title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="{{ route('commercial-registers.renew', $register->id) }}"
                                class="btn btn-sm btn-success" title="تجديد الصلاحية">
                                <i class="fas fa-sync-alt"></i>
                            </a>

                            <button type="button" class="btn btn-sm btn-danger delete-cr" data-id="{{ $register->id }}"
                                title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop


@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('.delete-cr').click(function (e) {
            e.preventDefault();
            let id = $(this).data('id');

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف السجل التجاري والملف المرفق نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف الآن',
                cancelButtonText: 'إلغاء',
                reverseButtons: true // لجعل "إلغاء" على اليمين بما أن الواجهة عربية
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-form-' + id).submit();
                }
            })
        });
    });
</script>
@stop
