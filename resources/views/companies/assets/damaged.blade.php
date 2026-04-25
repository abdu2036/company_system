@extends('layouts.admin')


@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h3 class="text-danger font-weight-bold mb-0"><i class="fas fa-recycle mr-2"></i> مخزن الأصول التالفة</h3>
            <p class="text-muted">إدارة الخسائر والأصول الخارجة عن الخدمة.</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-dark shadow-sm mr-2">
                <i class="fas fa-print"></i> طباعة التقرير
            </button>
            <a href="{{ route('assets.index') }}" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-list mr-1"></i> العودة للأصول النشطة
            </a>
        </div>
    </div>

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
                            <td class="align-middle">{{ $asset->updated_at->format('Y-m-d') }}</td>
                            <td class="align-middle no-print">
                                <button class="btn btn-sm btn-info" onclick="showAssetCard({{ $asset->id }})"><i class="fas fa-eye"></i></button>
                                <form action="{{ route('assets.restore', $asset->id) }}" method="POST" id="restore-form-{{ $asset->id }}" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-success" onclick="confirmRestore({{ $asset->id }})">
                                        <i class="fas fa-undo"></i> استعادة
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-muted">لا توجد أصول تالفة مطابقة للبحث</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection