@extends('layouts.admin')
@section('title', 'إدارة الشركات')

@section('content_header')
<h1 class="text-right">إدارة الشركات الأساسية</h1>
@stop

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('companies.index') }}" method="GET">
            <div class="row">
                <div class="col-md-10">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-left-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" name="search" class="form-control border-right-0"
                            placeholder="ابحث باسم الشركة، المفوض، أو رقم الهاتف..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info btn-block">
                        بحث
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card" dir="rtl">
    <div class="card-header bg-info">
        <h3 class="card-title float-right">بيانات الشركات المسجلة</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-right">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px">#</th> {{-- عمود الترقيم --}}
                        <th>اسم الشركة</th>
                        <th>النشاط</th>
                        <th>المفوض</th>
                        <th>الهاتف</th>
                        <th class="text-center">السجل التجاري</th>
                        <th class="text-center">الترخيص</th>
                        <th class="text-center">الغرفة التجارية</th>
                        <th class="text-center">سجل المستوردين</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                        <tr>
                            {{-- عرض رقم الصف تلقائياً --}}
                            <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>

                            <td>{{ $company->name }}</td>
                            <td>{{ $company->activity }}</td>
                            <td>{{ optional($company->commercialRegister)->representative_name ?? '---' }}</td>
                            <td>{{ optional($company->commercialRegister)->phone ?? '---' }}</td>

                            {{-- السجل التجاري --}}
                            <td class="text-center">
                                @if($company->commercialRegister && $company->commercialRegister->expiry_date)
                                    {!! \App\Http\Controllers\CompanyController::getStatusBadge($company->commercialRegister->expiry_date) !!}
                                @else
                                    <a href="{{ route('commercial-registers.create', ['company_id' => $company->id]) }}"
                                        class="badge badge-secondary hover-link">
                                        <i class="fas fa-plus-circle ml-1"></i> لا يوجد
                                    </a>
                                @endif
                            </td>

                            {{-- الترخيص المطور --}}
                            {{-- عمود الترخيص المعدل --}}
                            <td class="text-center">
                                {{-- نتحقق من وجود ترخيص ومن أن رقم الترخيص ليس فارغاً --}}
                                @if($company->license && !empty($company->license->license_number))
                                    {!! \App\Http\Controllers\CompanyController::getStatusBadge($company->license->expiry_date) !!}
                                @else
                                    {{-- إذا لم يوجد ترخيص، نظهر زر الإضافة --}}
                                    <a href="{{ route('licenses.create', ['company_id' => $company->id]) }}"
                                        class="badge badge-secondary hover-link">
                                        <i class="fas fa-plus-circle ml-1"></i> لا يوجد
                                    </a>
                                @endif
                            </td>

                            {{-- الغرفة التجارية --}}
                            <td class="text-center">
                                @if($company->chamber && $company->chamber->expiry_date)
                                    {!! \App\Http\Controllers\CompanyController::getStatusBadge($company->chamber->expiry_date) !!}
                                @else
                                    <a href="{{ route('chambers.create', ['company_id' => $company->id]) }}"
                                        class="badge badge-secondary hover-link">
                                        <i class="fas fa-plus-circle ml-1"></i> لا يوجد
                                    </a>
                                @endif
                            </td>

                            {{-- سجل المستوردين --}}
                            {{-- سجل المستوردين المطور --}}
                            {{-- عمود سجل المستوردين المعدل --}}
                            {{-- 2. قسم سجل المستوردين المعدل --}}
                            <td class="text-center">
                                @if($company->importer && !empty($company->importer->importer_number))
                                    {{-- نستخدم نفس الدالة لعرض حالة سجل المستوردين --}}
                                    {!! \App\Http\Controllers\CompanyController::getStatusBadge($company->importer->expiry_date) !!}
                                @else
                                    <a href="{{ route('importers.create', ['company_id' => $company->id]) }}"
                                        class="badge badge-secondary hover-link">
                                        <i class="fas fa-plus-circle ml-1"></i> لا يوجد
                                    </a>
                                @endif
                            </td>

                            <td class="text-center">
                                <form action="{{ route('companies.destroy', $company->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    {{-- السر هنا هو إضافة كلاس delete-btn فقط --}}
                                    <button type="button" class="btn btn-sm btn-danger delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .hover-link {
        transition: all 0.3s ease;
        text-decoration: none !important;
        display: inline-block;
        padding: 5px 10px;
        cursor: pointer;
        color: white !important;
    }

    .hover-link:hover {
        background-color: #4a545c !important;
        transform: translateY(-2px);
    }

    .badge {
        font-size: 0.85rem;
        padding: 7px;
        min-width: 110px;
    }

    /* تنسيق إضافي لعمود الترقيم */
    table td:first-child {
        background-color: #f8f9fa;
        color: #333;
    }
</style>
@stop