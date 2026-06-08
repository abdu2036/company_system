@extends('layouts.admin')

@section('title', 'التقارير المالية والإحصائية')

@section('content_header')
<div class="container-fluid font-arabic">
    <div class="row mb-2">
        <div class="col-sm-6 text-right">
            <h1 class="m-0 text-dark font-weight-bold">
                💵 منظومة التقارير المالية والإحصائية
            </h1>
        </div>
        <div class="col-sm-6 text-left d-flex justify-content-end align-items-center" style="gap: 10px;">
            {{-- زر الانتقال للتقرير السنوي المحمي بالصلاحية --}}
            @can('financial_reports.view')
                <a href="{{ route('reports.annual') }}" class="btn btn-primary shadow-sm font-weight-bold">
                    <i class="fas fa-calendar-alt ml-1"></i> 📅 تقرير الأرباح السنوي
                </a>
            @endcan

            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active">التقارير المالية</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid font-arabic text-right">

    {{-- قسم الإحصائيات المالية الشاملة (Small Boxes) --}}
    <h5 class="text-secondary font-weight-bold mb-3">
        <i class="fas fa-wallet ml-1 text-primary"></i> إحصائيات الحالة المالية الحالية:
    </h5>

    <div class="row">
        {{-- الصافي المالي الإجمالي - يتغير لونه ديناميكياً بناءً على القيمة --}}
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="small-box {{ ($netProfit ?? 0) >= 0 ? 'bg-success' : 'bg-danger' }} shadow-sm h-100 mb-0">
                <div class="inner text-white">
                    <h3>{{ number_format($netProfit ?? 0, 2) }} <sup style="font-size: 15px">د.ل</sup></h3>
                    <p class="font-weight-bold">
                        {{ ($netProfit ?? 0) >= 0 ? 'صافي الأرباح العام' : 'إجمالي العجز المالي (خسارة)' }}
                    </p>
                </div>
                <div class="icon">
                    <i class="fas {{ ($netProfit ?? 0) >= 0 ? 'fa-chart-line' : 'fa-chart-bar' }}"></i>
                </div>
                {{-- توجيه رابط النظرة التفصيلية إلى التقرير السنوي في حال امتلاك الصلاحية --}}
                <a href="{{ auth()->user()->can('financial_reports.view') ? route('reports.annual') : '#' }}" class="small-box-footer">
                    نظرة تفصيلية <i class="fas fa-arrow-circle-left mr-1"></i>
                </a>
            </div>
        </div>

        {{-- إجمالي الإيرادات --}}
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="small-box bg-success shadow-sm h-100 mb-0">
                <div class="inner text-white">
                    <h3>{{ number_format($totalRevenues ?? 0, 2) }} <sup style="font-size: 15px">د.ل</sup></h3>
                    <p class="font-weight-bold">إجمالي الإيرادات المسجلة</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-alt-circle-up"></i>
                </div>
                <a href="{{ route('revenues.index') }}" class="small-box-footer">انتقل إلى الإيرادات <i
                        class="fas fa-arrow-circle-left mr-1"></i></a>
            </div>
        </div>

        {{-- إجمالي المصروفات التشغيلية --}}
        <div class="col-lg-4 col-md-12 col-12 mb-3">
            <div class="small-box bg-danger shadow-sm h-100 mb-0">
                <div class="inner text-white">
                    <h3>{{ number_format($totalExpenses ?? 0, 2) }} <sup style="font-size: 15px">د.ل</sup></h3>
                    <p class="font-weight-bold">إجمالي المصروفات والتعاملات</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-alt-circle-down"></i>
                </div>
                <a href="{{ route('expenses.index') }}" class="small-box-footer">انتقل للمصروفات <i
                        class="fas fa-arrow-circle-left mr-1"></i></a>
            </div>
        </div>
    </div>

    {{-- قسم الرسوم البيانية والجداول الإحصائية --}}
    <div class="row mt-3">

        {{-- الجزء الأيمن: جدول ملخص الخزانة والحسابات المالية --}}
        <div class="col-md-7 col-sm-12 mb-4">
            <div class="card card-outline card-primary shadow-sm h-100">
                <div class="card-header border-0 bg-light py-3">
                    <h3 class="card-title float-right font-weight-bold mb-0" style="font-size: 1.1rem;">
                        <i class="fas fa-list-alt text-primary ml-1"></i> ملخص الإحصائيات الشاملة
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-valign-middle mb-0">
                        <thead>
                            <tr class="text-secondary" style="font-size: 14px;">
                                <th>البند المالي (التصنيف)</th>
                                <th class="text-center">إجمالي القيمة</th>
                                <th class="text-left pl-4">الحالة العامة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">💵 إجمالي المقبوضات والإيرادات</td>
                                <td class="text-center font-weight-bold text-success">
                                    {{ number_format($totalRevenues ?? 0, 2) }} د.ل</td>
                                <td class="text-left pl-4"><span class="badge badge-success px-2 py-1">نشط ومستمر</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">📉 المصروفات والنفقات التشغيلية</td>
                                <td class="text-center font-weight-bold text-danger">
                                    {{ number_format($totalExpenses ?? 0, 2) }} د.ل</td>
                                <td class="text-left pl-4"><span class="badge badge-danger px-2 py-1">تحت السيطرة</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">🏛️ الضرائب والاستقطاعات القانونية</td>
                                <td class="text-center font-weight-bold text-secondary">
                                    {{ number_format($totalTaxes ?? 0, 2) }} د.ل</td>
                                <td class="text-left pl-4"><span class="badge badge-secondary px-2 py-1">مجدولة</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white text-muted py-2 border-top-0">
                    <small class="float-right"><i class="fas fa-check-circle text-success ml-1"></i> يتم تحديث هذه
                        الإحصائيات المالية لحظياً من واقع الحركات المقيدة.</small>
                </div>
            </div>
        </div>

        {{-- الجزء الأيسر: الدائرة البيانية لتوزيع التدفقات المالية --}}
        <div class="col-md-5 col-sm-12 mb-4">
            <div class="card card-outline card-primary shadow-sm h-100">
                <div class="card-header border-0 bg-light py-3">
                    <h3 class="card-title float-right font-weight-bold mb-0" style="font-size: 1.1rem;">
                        <i class="fas fa-chart-pie text-primary ml-1"></i> توزيع التدفقات المالية العامة
                    </h3>
                </div>
                <div class="card-body p-0 d-flex flex-column justify-content-center align-items-center"
                    style="min-height: 250px;">
                    <div style="width: 80%; max-width: 240px; position: relative;">
                        <canvas id="financialFlowChart"></canvas>
                    </div>

                    {{-- دليل الألوان المخصص المحدث بعد حذف الرواتب --}}
                    <div class="d-flex justify-content-center flex-wrap mt-3 mb-3 w-100 font-weight-bold"
                        style="font-size: 12px; gap: 15px;">
                        <span><i class="fas fa-circle text-success ml-1"></i> إيرادات سارية</span>
                        <span><i class="fas fa-circle text-danger ml-1"></i> مصروفات تشغيلية</span>
                    </div>
                </div>
                <div class="card-footer bg-white text-muted py-2 border-top-0">
                    <small class="float-right"><i class="fas fa-check-circle text-success ml-1"></i> تحديث بياني فوري
                        وقائي.</small>
                </div>
            </div>
        </div>

    </div>
</div>
@stop

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

    .font-arabic {
        font-family: 'Cairo', sans-serif !important;
        text-align: right !important;
    }

    .small-box .icon {
        top: 15px !important;
        left: 15px !important;
        right: auto !important;
    }

    .table-valign-middle td {
        vertical-align: middle !important;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        var ctx = document.getElementById('financialFlowChart').getContext('2d');

        var totalRevenues = {{ $totalRevenues ?? 0 }};
        var totalExpenses = {{ $totalExpenses ?? 0 }};

        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['إيرادات سارية', 'مصروفات تشغيلية'],
                datasets: [{
                    data: [totalRevenues, totalExpenses],
                    backgroundColor: [
                        '#28a745', // الأخضر للإيرادات
                        '#dc3545'  // الأحمر للمصروفات
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '75%'
            }
        });
    });
</script>
@stop