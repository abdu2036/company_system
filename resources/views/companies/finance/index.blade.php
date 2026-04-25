@extends('layouts.admin')
@section('title', 'سجلات الشركات المالية')
@section('content')
<div class="container-fluid" style="direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title float-right">
                <i class="fas fa-list-alt ml-2"></i> سجلات الشركات المالية
            </h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover text-center">
                <thead class="bg-dark text-white">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 25%">اسم الشركة</th>
                        <th style="width: 20%">المفوض</th>
                        <th style="width: 20%" class="bg-secondary">إجمالي المتبقي (دينار)</th> {{-- العمود المضاف --}}
                        <th style="width: 30%">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                    <tr>
                        <td>{{ ($companies->currentPage() - 1) * $companies->perPage() + $loop->iteration }}</td>
                        <td class="font-weight-bold">{{ $company->name }}</td>
                        <td>
    {{ $company->commercialRegister->representative_name ?? 'غير محدد' }}
</td>
                        <td>
                            {{-- التحقق من وجود مديونية لعرضها بشكل بارز --}}
                            @if($company->total_debt > 0)
                                <span class="badge badge-danger p-2" style="font-size: 0.95rem; min-width: 100px;">
                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                    {{ number_format($company->total_debt, 2) }} د.ل
                                </span>
                            @else
                                <span class="badge badge-success p-2" style="font-size: 0.9rem;">
                                    <i class="fas fa-check-circle ml-1"></i> خالصة
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('finance.create', $company->id) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus ml-1"></i> إنشاء جديد
                                </a>
                                <a href="{{ route('finance.show', $company->id) }}" class="btn btn-info btn-sm mr-1">
                                    <i class="fas fa-eye ml-1"></i> عرض السجلات
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- روابط التنقل بين الصفحات --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection