@extends('layouts.admin')
@section('title', 'لوحة التحكم الاحترافية')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm border-0">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold">إجمالي الأصول</span>
                    <span class="info-box-number h4 mb-0">{{ $total_assets }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm border-0">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-double"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold">الأصول العاملة</span>
                    <span class="info-box-number h4 mb-0">{{ $active_assets }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm border-0">
                <span class="info-box-icon bg-warning text-white elevation-1"><i class="fas fa-tools"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold">تكاليف الصيانة</span>
                    <span class="info-box-number h4 mb-0">
                        {{ number_format($total_maintenance_cost, 2) }} 
                        <small class="font-weight-normal" style="font-size: 65%">د.ل</small>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm border-0">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text font-weight-bold">المخزن التالف</span>
                    <span class="info-box-number h4 mb-0">{{ $damaged_assets }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header border-0 bg-white pt-3">
                    <h3 class="card-title font-weight-bold text-muted">توزيع الأصول حسب الشركة</h3>
                </div>
                <div class="card-body">
                    <canvas id="assetsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header border-0 bg-white pt-3">
                    <h3 class="card-title font-weight-bold text-muted">أحدث الأصول</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @forelse($latest_assets ?? [] as $asset)
                        <li class="item">
                            <div class="product-info ml-3">
                                <a href="javascript:void(0)" class="product-title text-dark">{{ $asset->name }}
                                    <span class="badge badge-info float-right">{{ $asset->asset_code }}</span></a>
                                <span class="product-description text-xs">{{ $asset->company->name ?? 'بدون شركة' }}</span>
                            </div>
                        </li>
                        @empty
                        <li class="item text-center p-3 text-muted">لا توجد أصول مضافة مؤخراً</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center bg-white border-0">
                    <a href="{{ route('assets.index') }}" class="uppercase small font-weight-bold">عرض جميع الأصول</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('assetsChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chart_labels ?? []) !!},
                datasets: [{
                    data: {!! json_encode($chart_values ?? []) !!},
                    backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6c757d'],
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    });
</script>
@endsection