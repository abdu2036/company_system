@extends('layouts.admin')
@section('title', 'مخزن الأصول التالفة')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h4 class="font-weight-bold"><i class="fas fa-dumpster text-danger mr-2"></i> إدارة مخزن التوالف</h4>
        <div>
            <button onclick="window.print()" class="btn btn-dark shadow-sm mr-2">
                <i class="fas fa-print"></i> طباعة التقرير
            </button>
            <a href="{{ route('assets.index') }}" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-list mr-1"></i> العودة للأصول النشطة
            </a>
        </div>
    </div>

    {{-- قسم الفلترة --}}
    <div class="card mb-4 no-print shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body">
            <form action="{{ route('assets.damaged') }}" method="GET" class="row align-items-end">
                <div class="col-md-3">
                    <label class="small font-weight-bold">الشركة</label>
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
                    <label class="small font-weight-bold">بحث سريع</label>
                    <input type="text" name="search" class="form-control" placeholder="اسم أو كود الأصل..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">الشهر</label>
                    <select name="month" class="form-control">
                        <option value="">كل الشهور</option>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">السنة</label>
                    <select name="year" class="form-control">
                        @for($y=date('Y'); $y>=2023; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-danger btn-block">تطبيق الفلتر</button>
                </div>
            </form>
        </div>
    </div>

    {{-- بطاقات الإحصائيات --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-3">
                    <h6 class="text-uppercase small mb-2 opacity-75">عدد التوالف بالفترة</h6>
                    <h2 class="mb-0 font-weight-bold">{{ count($damaged_assets) }} <small>أصل</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-3">
                    <h6 class="text-uppercase small mb-2 opacity-75">قيمة الخسارة المحققة</h6>
                    <h2 class="mb-0 font-weight-bold">{{ number_format($damaged_assets->sum('purchase_price'), 2) }} <small>د.ل</small></h2>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول البيانات --}}
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>كود الأصل</th>
                            <th>الشركة التابعة</th>
                            <th>اسم الأصل</th>
                            <th>سعر التكلفة</th>
                            <th>المسؤول (الفني)</th>
                            <th>تاريخ التلف</th>
                            <th class="no-print">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($damaged_assets as $asset)
                        <tr>
                            <td class="align-middle"><span class="badge badge-secondary">{{ $asset->asset_code }}</span></td>
                            <td class="align-middle font-weight-bold text-primary">{{ $asset->company->name }}</td>
                            <td class="align-middle">{{ $asset->name }}</td>
                            <td class="align-middle text-danger">{{ number_format($asset->purchase_price, 2) }} د.ل</td>
                            
                            {{-- عرض اسم الفني المسؤول --}}
                         <td class="align-middle">
    @php
        // جلب آخر سجل صيانة مرتبط
        $lastLog = $asset->maintenanceLogs->last();
    @endphp

    @if($lastLog && $lastLog->technician)
        <span class="badge badge-pill badge-outline-dark">
            <i class="fas fa-user-wrench mr-1"></i>
            {{ $lastLog->technician->name }}
        </span>
    @else
        <span class="text-muted small">غير محدد</span>
    @endif
</td>

                            <td class="align-middle">{{ $asset->updated_at->format('Y-m-d') }}</td>
                            
                            <td class="align-middle no-print">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info" onclick="showAssetCard({{ $asset->id }})" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- زر تأكيد الاستلام --}}
                                    @if(!$asset->received_at)
                                        <form action="{{ route('assets.confirm-receipt', $asset->id) }}" method="POST" id="confirm-form-{{ $asset->id }}" class="d-inline ml-1">
                                            @csrf
                                            <button type="button" onclick="confirmReceipt({{ $asset->id }})" class="btn btn-sm btn-warning font-weight-bold" title="تأكيد استلام بالمخزن">
                                                <i class="fas fa-check-circle"></i> استلام
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge badge-success ml-1"><i class="fas fa-box-open"></i> مستلم</span>
                                    @endif

                                    {{-- زر استعادة الأصل --}}
                                    <form action="{{ route('assets.restore', $asset->id) }}" method="POST" id="restore-form-{{ $asset->id }}" class="d-inline ml-1">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="confirmRestore({{ $asset->id }})" title="استعادة للأصول النشطة">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-muted">لا توجد أصول تالفة مطابقة للبحث</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- سكريبتات التأكيد والاستعادة --}}
<script>
    // 1. دالة تأكيد الاستلام
    function confirmReceipt(id) {
        Swal.fire({
            title: 'تأكيد استلام أصل تالف؟',
            text: "بموافقتك، سيتم تسجيلك كمستلم رسمي لهذا الأصل في مخزن التوالف لبدء عملية الفحص",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، تم الاستلام',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('confirm-form-' + id).submit();
            }
        })
    }

    // 2. دالة استعادة الأصل (الإصلاح المطلوب)
    function confirmRestore(id) {
        Swal.fire({
            title: 'هل تريد استعادة الأصل؟',
            text: "سيتم نقل الأصل من مخزن التوالف إلى القائمة النشطة بحالة (مستعمل)",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، استعادة الآن',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('restore-form-' + id).submit();
            }
        })
    }

    // دالة عرض التفاصيل (Modal)
    function showAssetCard(id) {
        // يمكنك إضافة كود جلب البيانات عبر Ajax هنا كما في بقية مشروعك
        alert('جاري عرض تفاصيل الأصل رقم: ' + id);
    }
</script>

<style>
    @media print {
        .no-print { display: none !important; }
        .card { border: 1px solid #ddd !important; shadow: none !important; }
    }
    .badge-outline-dark {
        color: #343a40;
        border: 1px solid #343a40;
        background-color: transparent;
    }
</style>
@endsection