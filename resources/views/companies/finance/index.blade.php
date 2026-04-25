@extends('layouts.admin')
@section('title', 'سجلات الشركات المالية')
@section('content')
<div class="container-fluid" style="direction: rtl; text-align: right;">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title float-right">سجلات الشركات المالية</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>#</th>
                        <th>اسم الشركة</th>
                        <th>المفوض</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->representative_name ?? 'غير محدد' }}</td>
                       <td>
    <a href="{{ route('finance.create', $company->id) }}" class="btn btn-success btn-sm">
        <i class="fas fa-plus"></i> إنشاء جديد
    </a>
    <a href="{{ route('finance.show', $company->id) }}" class="btn btn-info btn-sm">
        <i class="fas fa-eye"></i> عرض السجلات
    </a>
</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection