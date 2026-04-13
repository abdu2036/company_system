@extends('layouts.admin')

@section('title', 'سجل المستوردين')

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
                <a href="{{ route('importers.create') }}" class="btn btn-info btn-block shadow-sm font-weight-bold">
                    <i class="fas fa-plus-circle"></i> إضافة سجل مستورد جديد
                </a>
            </div>

            <div class="col-md-9">
                <form action="{{ url('/companies/importers') }}" method="GET" class="m-0">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="ابحث برقم السجل أو اسم الشركة..." value="{{ request('search') }}"
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
        <h3 class="card-title float-right">بيانات سجلات المستوردين المسجلة 📋</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover text-center" style="direction: rtl;">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>اسم الشركة 🏢</th>
                    <th>رقم السجل 🔢</th>
                    <th>تاريخ الإصدار 📅</th>
                    <th>تاريخ الانتهاء ⏳</th>
                    <th>المرفق 📁</th>
                    <th>العمليات ⚙️</th>
                </tr>
            </thead>
            <tbody>
                {{-- نستخدم @forelse للتعامل مع الحالة التي لا توجد فيها بيانات --}}
                @forelse($importers as $importer)
                    {{-- نتحقق هنا برمجياً لضمان عدم عرض السجلات الفارغة تماماً --}}
                    @if(!empty($importer->importer_number))
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $importer->company->name ?? 'غير محدد' }}</td>
                            <td>{{ $importer->importer_number }}</td>
                            <td>{{ $importer->issue_date }}</td>
                            <td>
                                @php
                                    $expiryDate = \Carbon\Carbon::parse($importer->expiry_date);
                                    $today = \Carbon\Carbon::today();
                                    $isExpiringSoon = $expiryDate->isAfter($today) && $expiryDate->diffInDays($today) <= 30;
                                    $isExpired = $expiryDate->isBefore($today);
                                @endphp

                                @if($isExpired)
                                    <span class="badge badge-danger p-2 shadow-sm w-100">
                                        <i class="fas fa-exclamation-triangle"></i> منتهي ({{ $importer->expiry_date }})
                                    </span>
                                @elseif($isExpiringSoon)
                                    <span class="badge badge-warning p-2 shadow-sm w-100">
                                        <i class="fas fa-clock"></i> ينتهي قريباً ({{ $importer->expiry_date }})
                                    </span>
                                @else
                                    <span class="badge badge-success p-2 shadow-sm w-100">
                                        <i class="fas fa-check-circle"></i> ساري ({{ $importer->expiry_date }})
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($importer->document_path)
                                    <a href="{{ asset($importer->document_path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf"></i> عرض المرفق
                                    </a>
                                @else
                                    <span class="text-muted small">لا يوجد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- 1. زر التعديل (Edit) --}}
                                    <a href="{{ route('importers.edit', $importer->id) }}"
                                        class="btn btn-sm btn-primary text-white mx-1" style="width: 35px;"
                                        title="تعديل البيانات">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- 2. زر التجديد (Renew) --}}
                                    <a href="{{ route('importers.renew', $importer->id) }}"
                                        class="btn btn-sm btn-success text-white mx-1" style="width: 35px;" title="تجديد السجل">
                                        <i class="fas fa-sync-alt"></i>
                                    </a>

                                    {{-- 3. زر الحذف (Delete) --}}
                                    <form action="{{ route('importers.destroy', $importer->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        {{-- السر هنا هو إضافة كلاس delete-btn فقط --}}
                                        <button type="button" class="btn btn-sm btn-danger delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="text-center">لا توجد سجلات مستوردين مسجلة حالياً 📭</td>
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

    .gap-1 {
        gap: 5px;
    }
</style>
@stop