@extends('layouts.admin') 
@section('title', 'سجل صيانة الأصول')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2 class="h4">سجل صيانة الأصول الشامل</h2>
        <div>
            <button onclick="window.print()" class="btn btn-success btn-sm ml-2">
                <i class="fas fa-print"></i> طباعة التقرير
            </button>
            <a href="{{ route('assets.index') }}" class="btn btn-outline-primary btn-sm">العودة للأصول</a>
        </div>
    </div>

    <div class="card mb-4 no-print shadow-sm">
        <div class="card-body">
        <div class="card-body">
    <form action="{{ route('assets.maintenance_logs') }}" method="GET" class="row">
        <div class="col-md-3">
            <select name="company_id" class="form-control" onchange="this.form.submit()">
                <option value="">كل الشركات</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="اسم الأصل أو الكود..." value="{{ request('search') }}">
        </div>
        
        <div class="col-md-2">
            <select name="month" class="form-control">
                <option value="">كل الأشهر</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>شهر {{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <select name="year" class="form-control">
                <option value="">كل السنوات</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>سنة {{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-block">بحث</button>
        </div>
    </form>
</div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-dark text-white shadow-sm">
                <div class="card-body text-center">
                    <h6>إجمالي التكاليف (للمدة المختارة)</h6>
                    <h3 class="mb-0">{{ number_format($total_maintenance_cost, 2) }} د.ل</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 text-center">
                <thead class="bg-light">
                    <tr>
                        <th>تاريخ الصيانة</th>
                        <th>كود الأصل</th>
                        <th>اسم الأصل</th>
                        <th>نوع الصيانة</th>
                        <th>التكلفة</th>
                        <th>التفاصيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->start_date }}</td>
                        <td><span class="badge badge-info">{{ $log->asset->asset_code ?? 'N/A' }}</span></td>
                        <td>{{ $log->asset->name ?? 'أصل محذوف' }}</td>
                        <td>{{ $log->maintenance_type }}</td>
                        <td class="text-danger fw-bold">{{ number_format($log->cost, 2) }} د.ل</td>
                        <td><small>{{ $log->details ?: '---' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-muted">لا توجد سجلات مطابقة للبحث</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* تنسيق خاص للطباعة */
@media print {
    .no-print { display: none !important; }
    .card { border: none !important; shadow: none !important; }
    .table { width: 100% !important; border-collapse: collapse; }
    .table th, .table td { border: 1px solid #ddd !important; padding: 8px; }
}
</style>
@endsection