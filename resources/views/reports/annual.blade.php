@extends('layouts.admin')

@section('title', 'تقرير الأرباح والخسائر السنوي')

@section('content')
    <div class="container-fluid font-arabic text-right pt-3">
        
        {{-- الترويسة والعنوان الرئيسي وزر الطباعة (تم دمجها هنا لضمان الظهور الفوري) --}}
        <div class="row mb-4 align-items-center no-print">
            <div class="col-sm-6 text-right">
                <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.8rem;">📅 تقرير الأرباح والخسائر السنوي</h1>
            </div>
            <div class="col-sm-6 text-left">
                <button onclick="window.print();" class="btn btn-primary shadow-sm px-4 font-weight-bold">
                    <i class="fas fa-print ml-1"></i> طباعة التقرير الشامل
                </button>
            </div>
        </div>

        
        
        {{-- صندوق الفلترة واختيار السنة (يختفي عند الطباعة تلقائياً) --}}
        <div class="card card-outline card-secondary shadow-sm mb-4 no-print">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('reports.annual') }}" class="form-inline justify-content-start" style="gap: 15px;">
                    <label for="year" class="font-weight-bold ml-2 mb-0">اختر السنة المالية:</label>
                    <select name="year" id="year" class="form-control select2">
                        @for($i = Carbon\Carbon::now()->year; $i >= Carbon\Carbon::now()->year - 5; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-dark px-4">تحديث البيانات</button>
                </form>
            </div>
        </div>

        {{-- ملخص الأرقام السنوية الكبيرة --}}
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="info-box shadow-sm border">
                    <span class="info-box-icon bg-success"><i class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text font-weight-bold">إجمالي إيرادات السنة</span>
                        <span class="info-box-number text-success" style="font-size: 1.3rem;">{{ number_format($totalRevenues, 2) }} د.ل</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="info-box shadow-sm border">
                    <span class="info-box-icon bg-danger"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text font-weight-bold">إجمالي مصروفات السنة</span>
                        <span class="info-box-number text-danger" style="font-size: 1.3rem;">{{ number_format($totalExpenses, 2) }} د.ل</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="info-box shadow-sm border">
                    <span class="info-box-icon {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }}"><i class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text font-weight-bold">{{ $netProfit >= 0 ? 'صافي أرباح السنة' : 'العجز السنوي المالي' }}</span>
                        <span class="info-box-number {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.3rem;">{{ number_format($netProfit, 2) }} د.ل</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- جدول التدفق المالي المفصل شهراً بشهر --}}
        <div class="card card-outline card-primary shadow-sm mt-3">
            <div class="card-header bg-light py-3">
                <h3 class="card-title float-right font-weight-bold mb-0" style="font-size: 1.1rem;">
                    <i class="fas fa-calendar-alt text-primary ml-1"></i> حركة التدفق المالي التفصيلية للأشهر (1 - 12)
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped text-center mb-0">
                    <thead class="bg-secondary text-white" style="font-size: 14px;">
                        <tr>
                            <th>الشهر</th>
                            <th>🟢 الإيرادات والمقبوضات</th>
                            <th>🔴 المصروفات والنفقات</th>
                            <th>📊 الصافي المالي للشهر</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $monthsArabic = [
                                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 
                                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس', 
                                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                            ];
                        @endphp
                        @foreach($monthlyData as $monthNumber => $data)
                            <tr>
                                <td class="font-weight-bold">{{ $monthNumber }} - {{ $monthsArabic[$monthNumber] }}</td>
                                <td class="text-success font-weight-bold">{{ number_format($data['revenues'], 2) }} د.ل</td>
                                <td class="text-danger font-weight-bold">{{ number_format($data['expenses'], 2) }} د.ل</td>
                                <td class="{{ $data['profit'] >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                    {{ number_format($data['profit'], 2) }} د.ل
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
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        .font-arabic { font-family: 'Cairo', sans-serif !important; text-align: right !important; }
        .print-only { display: none; }
        
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .main-footer, .main-header, .content-header, .brand-link, .main-sidebar { display: none !important; }
            .card { border: none !important; box-shadow: none !important; }
            .content-wrapper { margin-right: 0 !important; padding: 0 !important; width: 100% !important; }
            body { background: #fff !important; direction: rtl !important; }
        }
    </style>
@stop