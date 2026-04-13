@extends('layouts.admin')

@section('title', 'لوحة التقارير العامة')

@section('content')
<div class="content-wrapper" style="margin-right: 0px;">
    <div class="container-fluid pt-4">
        <h2 class="mb-4 text-right">📊 إحصائيات النظام الشاملة</h2>

        {{-- قسم المربعات الإحصائية --}}
        <div class="row custom-row-stats text-right" dir="rtl">
            
            {{-- 1. إجمالي الشركات --}}
            <div class="custom-col">
                <a href="{{ route('companies.index') }}" class="text-decoration-none">
                <div class="small-box bg-info shadow">
                    <div class="inner">
                        <h3>{{ $totalCompanies }}</h3>
                        <p>إجمالي الشركات</p>
                    </div>
                    <div class="icon"><i class="fas fa-building"></i></div>
                    <div class="small-box-footer">نظرة عامة</div>
                </div>
                </a>
            </div>

            {{-- 2. سجلات تجارية منتهية --}}
            <div class="custom-col">
                <a href="{{ route('commercial-registers.index', ['status' => 'expired']) }}" class="text-decoration-none">
                    <div class="small-box bg-danger shadow">
                        <div class="inner">
                            <h3>{{ $expiredRegisters }}</h3>
                            <p>سجل تجاري منتهي ⚠️</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-contract"></i></div>
                        <div class="small-box-footer">انتقل للتجديد <i class="fas fa-arrow-circle-right"></i></div>
                    </div>
                </a>
            </div>

            {{-- 3. تراخيص منتهية --}}
            <div class="custom-col">
                <a href="{{ route('licenses.index', ['status' => 'expired']) }}" class="text-decoration-none">
                    <div class="small-box bg-danger shadow">
                        <div class="inner">
                            <h3>{{ $expiredLicenses }}</h3>
                            <p>تراخيص منتهية ⚠️</p>
                        </div>
                        <div class="icon"><i class="fas fa-certificate"></i></div>
                        <div class="small-box-footer">انتقل للتجديد <i class="fas fa-arrow-circle-right"></i></div>
                    </div>
                </a>
            </div>

            {{-- 4. اشتراكات غرفة منتهية --}}
            <div class="custom-col">
                <a href="{{ route('chambers.index', ['status' => 'expired']) }}" class="text-decoration-none">
                    <div class="small-box bg-danger shadow">
                        <div class="inner">
                            <h3>{{ $expiredChamber }}</h3>
                            <p>غرفة تجارية ⚠️</p>
                        </div>
                        <div class="icon"><i class="fas fa-store-alt"></i></div>
                        <div class="small-box-footer">انتقل للتجديد <i class="fas fa-arrow-circle-right"></i></div>
                    </div>
                </a>
            </div>

            {{-- 5. سجل مستوردين منتهي --}}
            <div class="custom-col">
                <a href="{{ route('importers.index', ['status' => 'expired']) }}" class="text-decoration-none">
                    <div class="small-box bg-danger shadow">
                        <div class="inner">
                            <h3>{{ $expiredImporters }}</h3>
                            <p>سجل مستوردين ⚠️</p>
                        </div>
                        <div class="icon"><i class="fas fa-ship"></i></div>
                        <div class="small-box-footer">انتقل للتجديد <i class="fas fa-arrow-circle-right"></i></div>
                    </div>
                </a>
            </div>
        </div>

        {{-- الصف الثاني: الجداول --}}
        <div class="row mt-4">
<div class="col-md-6">
    <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-navy text-white">
            <h3 class="card-title float-right">
                <i class="fas fa-chart-pie ml-2"></i> ملخص الإحصائيات الشاملة
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover text-right mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 60%">البند (القطاع)</th>
                        <th class="text-center">إجمالي العدد</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 1. إجمالي السجلات التجارية --}}
                    <tr>
                        <td>
                            <i class="fas fa-id-card text-primary ml-2"></i> 
                            <strong>إجمالي السجلات التجارية</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-primary px-3 py-2" style="font-size: 14px;">
                                {{ $totalRegisters }}
                            </span>
                        </td>
                    </tr>

                    {{-- 2. إجمالي التراخيص --}}
                    <tr>
                        <td>
                            <i class="fas fa-certificate text-success ml-2"></i> 
                            <strong>إجمالي التراخيص</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success px-3 py-2" style="font-size: 14px;">
                                {{ $totalLicenses }}
                            </span>
                        </td>
                    </tr>

                    {{-- 3. إجمالي الغرف التجارية --}}
                    <tr>
                        <td>
                            <i class="fas fa-store text-warning ml-2"></i> 
                            <strong>إجمالي الغرف التجارية</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-warning text-white px-3 py-2" style="font-size: 14px;">
                                {{ $totalChamber }}
                            </span>
                        </td>
                    </tr>

                    {{-- 4. إجمالي المستوردين --}}
                    <tr>
                        <td>
                            <i class="fas fa-ship text-secondary ml-2"></i> 
                            <strong>إجمالي المستوردين</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary px-3 py-2" style="font-size: 14px;">
                                {{ $totalImporters }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white text-muted small">
            <i class="fas fa-check-circle text-success ml-1"></i> يتم تحديث هذه الإجماليات لحظياً من قاعدة البيانات.
        </div>
    </div>
</div>

     <div class="col-md-6">
    <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-navy text-white">
            <h3 class="card-title float-right">
                <i class="nav-icon fas fa-chart-line ml-2"></i> توزيع الحالة العامة
            </h3>
        </div>
    <div class="card-body">
        <canvas id="statusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
    </div>
            <div class="card-footer bg-white text-muted small">
            <i class="fas fa-check-circle text-success ml-1"></i> يتم تحديث هذه الإجماليات لحظياً من قاعدة البيانات.
        </div>
</div>

        </div>
    </div>
</div>

{{-- تنسيق CSS لجعل المربعات الـ 5 في صف واحد --}}
<style>
    /* تقسيم السطر لـ 5 أعمدة متساوية في الشاشات الكبيرة */
    .custom-row-stats {
        display: flex;
        flex-wrap: wrap;
    }
    
    .custom-col {
        flex: 0 0 20%; /* 100% / 5 مربعات */
        max-width: 20%;
        padding: 0 7px;
    }

    /* استجابة الشاشات المتوسطة (مربعين في الصف) */
    @media (max-width: 992px) {
        .custom-col {
            flex: 0 0 50%;
            max-width: 50%;
            margin-bottom: 15px;
        }
    }

    /* استجابة الشاشات الصغيرة (مربع واحد في الصف) */
    @media (max-width: 576px) {
        .custom-col {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    /* لمسات جمالية للمربعات */
    .small-box {
        border-radius: 12px !important;
        overflow: hidden;
        transition: transform 0.3s;
    }
    .small-box:hover {
        transform: translateY(-5px);
    }
    .small-box .inner p {
        font-weight: bold;
        font-size: 14px !important;
    }
    .small-box h3 {
        font-size: 28px !important;
    }
</style>
@stop
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('statusChart').getContext('2d');
        
        // 1. حساب البيانات القريبة من الانتهاء (تنبيه)
        var totalWarning = {{ $nearExpiryChamber }}; // يمكنك إضافة متغيرات أخرى هنا

        // 2. حساب البيانات المنتهية فعلياً
        var totalExpired = {{ $expiredLicenses + $expiredImporters + $expiredRegisters + $expiredChamber }};

        // 3. حساب البيانات السارية (نطرح منها التنبيهات لكي لا يتكرر العدد)
        var totalActiveAll = {{ $activeLicenses + $activeImporters + ($totalRegisters - $expiredRegisters) }};
        var totalCleanActive = totalActiveAll - totalWarning;

        var statusChart = new Chart(ctx, {
            type: 'doughnut', // تغيير النوع لـ Doughnut يعطي مظهراً عصرياً أكثر
            data: {
                labels: ['بيانات سارية', 'تنتهي قريباً ⚠️', 'بيانات منتهية ❌'],
                datasets: [{
                    data: [totalCleanActive, totalWarning, totalExpired],
                    backgroundColor: [
                        '#28a745', // أخضر للسارية
                        '#ffc107', // أصفر للتنبيه (قرب الانتهاء)
                        '#dc3545'  // أحمر للمنتهية
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            font: { family: 'Cairo', size: 13 },
                            padding: 20
                        }
                    }
                },
                cutout: '60%' // يجعل الدائرة مفرغة من المنتصف
            }
        });
    });
</script>