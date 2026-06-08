@extends('layouts.admin')
@section('title', 'سجلات إيرادات الشركات')
@section('content')
<div class="container-fluid" style="direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
    <div class="card card-success card-outline shadow-sm">
        <div class="card-header">
            <h3 class="card-title float-right">
                <i class="fas fa-hand-holding-usd ml-2 text-success"></i> سجلات إيرادات الشركات التشغيلية والمالية
            </h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover text-center">
                <thead class="bg-dark text-white">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 25%">اسم الشركة</th>
                        <th style="width: 20%">المفوض</th>
                        <th style="width: 20%" class="bg-secondary">إجمالي الإيرادات (دينار)</th>
                        <th style="width: 30%">العمليات المالية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                    <tr>
                        {{-- الترقيم التلقائي المتوافق مع باجينيشن نظامك الحالي --}}
                        <td>{{ ($companies->currentPage() - 1) * $companies->perPage() + $loop->iteration }}</td>
                        <td class="font-weight-bold">{{ $company->name }}</td>
                        <td>
                            {{ $company->commercialRegister->representative_name ?? 'غير محدد' }}
                        </td>
                        <td>
                            @if($company->revenues_sum_amount > 0)
                                <span class="badge badge-success p-2" style="font-size: 0.95rem; min-width: 100px;">
                                    <i class="fas fa-arrow-down ml-1"></i>
                                    {{ number_format($company->revenues_sum_amount, 2) }} د.ل
                                </span>
                            @else
                                <span class="badge badge-light border text-muted p-2" style="font-size: 0.9rem;">
                                    <i class="fas fa-minus ml-1"></i> 0.00 د.ل
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                {{-- زر إضافة حركة إيراد جديدة --}}
                                <a href="{{ route('revenues.create', ['company_id' => $company->id]) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus ml-1"></i> تسجيل إيراد
                                </a>
                                {{-- زر الانتقال إلى كشف الحركات التفصيلي --}}
                                <a href="{{ route('revenues.history', $company->id) }}" class="btn btn-info btn-sm mr-1">
                                    <i class="fas fa-eye ml-1"></i> عرض الحركات
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- روابط التنقل الذكي بين الصفحات المتوافقة تماماً مع نظامك --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection