@extends('layouts.admin')

@section('title', 'إدارة الأصول النشطة')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-4">
                <h1 class="m-0 text-bold text-dark">
                    <i class="fas fa-boxes mr-2 text-primary"></i>قائمة الأصول النشطة
                </h1>
            </div>

            <div class="col-sm-4">
                <form action="{{ route('assets.index') }}" method="GET" id="filterForm">
                    <div class="input-group shadow-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-filter text-muted"></i></span>
                        </div>
                        <select name="company_id" class="form-control"
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="">عرض أصول جميع الشركات</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="col-sm-4 text-right">
                @can('view asset statistics')
                <a href="{{ route('assets.dashboard') }}" class="btn btn-outline-secondary shadow-sm mr-2">
                    <i class="fas fa-chart-pie mr-1"></i> الإحصائيات
                </a>
                @endcan
                @can('create assets')
                <a href="{{ route('assets.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle mr-1"></i> إضافة أصل جديد
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-white py-3">
                <h3 class="card-title text-muted mt-1">
                    <i class="fas fa-list mr-2"></i> جميع الأصول المسجلة حالياً
                </h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="table_search" class="form-control float-right"
                            placeholder="بحث عن أصل...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap text-center align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th style="width: 10%">كود الأصل</th>
                            <th>الشركة التابعة</th>
                            <th>اسم الأصل</th>
                            <th>التصنيف</th>
                            <th>الموقع</th>
                            <th>الحالة</th>
                            <th style="width: 15%">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td class="text-center">
                                <span class="badge badge-secondary px-2 py-1">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <span class="badge badge-primary-soft p-2 font-weight-bold"
                                    style="background: #e7f1ff; color: #007bff; border-radius: 8px;">
                                    {{ $asset->asset_code }}
                                </span>
                            </td>
                            <td class="font-weight-bold text-muted">{{ $asset->company->name }}</td>
                            <td>{{ $asset->name }}</td>
                            <td><span class="badge badge-secondary shadow-sm">{{ $asset->category }}</span></td>
                            <td><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $asset->location }}</td>
                            <td>
                                @if($asset->status == 'جديد')
                                <span class="badge badge-success px-3 py-2"><i class="fas fa-star mr-1"></i> جديد</span>
                                @elseif($asset->status == 'مستعمل')
                                <span class="badge badge-info px-3 py-2"><i class="fas fa-sync-alt mr-1"></i>
                                    مستعمل</span>
                                @elseif($asset->status == 'تحت الصيانة')
                                <span class="badge badge-warning px-3 py-2 text-dark"><i class="fas fa-tools mr-1"></i>
                                    تحت الصيانة</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group shadow-sm" style="border-radius: 20px; overflow: hidden;">
                                    {{-- زر الصيانة السريع --}}
                                    {{-- زر الصيانة السريع --}}
                                    @canany(['edit assets', 'manage maintenance','technician'])
                                    @if($asset->status != 'تحت الصيانة')
                                    <form action="{{ route('assets.maintenance', $asset->id) }}" method="POST"
                                        id="maintenance-form-{{ $asset->id }}" style="display:inline;">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-warning" title="إرسال للصيانة"
                                            onclick="confirmMaintenance({{ $asset->id }})">
                                            <i class="fas fa-tools text-dark"></i>
                                        </button>
                                    </form>
                                    @else
                                    {{-- زر لإنهاء الصيانة يفتح المودال --}}
                                    <button type="button" class="btn btn-sm btn-success"
                                        onclick="openCompleteMaintenanceModal({{ $asset->id }}, '{{ $asset->name }}')"
                                        title="إتمام الصيانة وتوثيق التكلفة">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                    @endif
                                    @endcanany
                                    {{-- زر الـ QR Code --}}
                                    <button class="btn btn-sm btn-info border-left" data-toggle="modal"
                                        data-target="#qrModal{{ $asset->id }}" title="عرض الـ QR">
                                        <i class="fas fa-qrcode"></i>
                                    </button>

                                    {{-- زر التعديل --}}

                                    <a href="{{ route('assets.edit', $asset->id) }}"
                                        class="btn btn-sm btn-light border-left border-right" title="تعديل">
                                        <i class="fas fa-edit text-secondary"></i>
                                    </a>


                                    {{-- زر النقل للتوالف --}}

                                    @canany(['edit assets', 'manage damaged'])
                                    <form action="{{ route('assets.move_to_damaged', $asset->id) }}" method="POST"
                                        id="damage-form-{{ $asset->id }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="btn btn-sm btn-danger" title="نقل للتوالف"
                                            onclick="confirmDamage({{ $asset->id }})">
                                            <i class="fas fa-boxes"></i>
                                        </button>
                                    </form>
                                    @endcanany
                                    {{-- Modal الـ QR Code --}}
                                    <div class="modal fade" id="qrModal{{ $asset->id }}" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow-lg">

                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">بطاقة الأصل: {{ $asset->asset_code }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center py-4">
                                                    <div class="mb-3 p-3 d-inline-block shadow-sm"
                                                        style="background: #fff; border: 1px solid #eee; border-radius: 15px;">
                                                        {!! QrCode::size(220)
                                                        ->color(30, 79, 156)
                                                        ->encoding('UTF-8')
                                                        ->generate("الأصل: " . $asset->name . "\nالكود: " .
                                                        $asset->asset_code)
                                                        !!}
                                                    </div>
                                                    <h4 class="font-weight-bold text-dark mt-2">{{ $asset->name }}</h4>
                                                    <p class="text-muted small">امسح الكود للحصول على بيانات الأصل</p>
                                                </div>
                                                <div class="modal-footer bg-light justify-content-center">
 <button class="btn btn-sm btn-primary" onclick="printBarcode(
    '{{ $asset->asset_code ?? $asset->code }}', 
    '{{ $asset->name }}', 
    '{{ is_object($asset->company) ? optional($asset->company)->name : ($asset->company ?? '---') }}', 
    '{{ is_object($asset->location) ? optional($asset->location)->name : ($asset->location ?? '---') }}'
)">
    <i class="fas fa-print"></i> طباعة الملصق
</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80"
                                    class="mb-3 opacity-50">
                                <p class="text-muted">لا توجد أصول مضافة حالياً في هذا القسم.</p>
                                <a href="{{ route('assets.create') }}" class="btn btn-sm btn-primary">أضف أول أصل
                                    الآن</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- المودال الخاص بتوثيق الصيانة (يُعرف مرة واحدة فقط خارج الـ Loop) --}}
    <div class="modal fade" id="completeMaintenanceModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-tools mr-2"></i> توثيق صيانة: <span
                            id="m_asset_name"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="maintenanceForm" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-group text-right">
                            <label>نوع الصيانة <span class="text-danger">*</span></label>
                            <select name="maintenance_type" class="form-control" required>
                                <option value="إصلاح عطل">إصلاح عطل مفاجئ</option>
                                <option value="صيانة دورية">صيانة دورية</option>
                                <option value="تغيير قطع">تغيير قطع غيار</option>
                                <option value="تحديث">تحديث / تطوير (Upgrade)</option>
                            </select>
                        </div>
                        <div class="form-group text-right">
                            <label>تكلفة الصيانة (د.ل) <span class="text-danger">*</span></label>
                            <input type="number" name="cost" class="form-control" placeholder="0.00" step="0.01"
                                required>
                        </div>
                        <div class="form-group text-right">
                            <label>تفاصيل الإصلاح<span class="text-danger">*</span></label>
                            <textarea name="details" class="form-control" rows="3" placeholder="ما الذي تم إصلاحه؟"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success px-4">حفظ وإعادة للأصول النشطة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
/**
 * دالة لفتح مودال إتمام الصيانة وتجهيز الرابط
 */
function openCompleteMaintenanceModal(id, name) {
    // تحديد اسم الأصل في عنوان المودال
    document.getElementById('m_asset_name').innerText = name;

    // تجهيز رابط الفورم
    let url = "{{ route('assets.complete-maintenance', ':id') }}";
    url = url.replace(':id', id);

    // إسناد الرابط للفورم
    document.getElementById('maintenanceForm').setAttribute('action', url);

    // عرض المودال
    $('#completeMaintenanceModal').modal('show');
}
</script>

<style>
/* التنسيقات العادية للمتصفح */
.table thead th {
    border-top: 0;
    text-transform: uppercase;
    font-size: 13px;
    color: #6c757d;
}

.table td {
    vertical-align: middle !important;
}

.badge {
    font-weight: 500;
    border-radius: 6px;
}

.btn-group .btn {
    padding: 0.4rem 0.8rem;
}

.modal-content {
    border-radius: 15px;
    overflow: hidden;
}

.modal-header {
    border-bottom: 0;
}

.modal-footer {
    border-top: 0;
}

/* ========================================== */
/* تنسيقات الطباعة الحرارية (80mm)           */
/* ========================================== */

</style>

<script>
function confirmMaintenance(assetId) {
    Swal.fire({
        title: 'تحويل إلى الصيانة؟',
        text: "هل تريد فعلاً تغيير حالة هذا الأصل إلى 'تحت الصيانة'؟",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f39c12', // لون برتقالي يتناسب مع زر الصيانة
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، أرسل للصيانة',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // إرسال النموذج في حال ضغط المستخدم على "حسناً"
            document.getElementById('maintenance-form-' + assetId).submit();
        }
    })
}
</script>

<script>
function printBarcode(assetCode, assetName, companyName, locationName) {
    // جلب الـ SVG الفعلي المولد للـ QR Code من المودال النشط
    var qrSvgElement = document.querySelector('.modal.show .modal-body svg');
    
    if (!qrSvgElement) {
        alert('يرجى فتح بطاقة الأصل أولاً ليتمكن النظام من قراءة الـ QR Code.');
        return;
    }

    var qrHtml = qrSvgElement.outerHTML;

    // فتح نافذة طباعة مستقلة ونظيفة جداً
    var printWindow = window.open('', '_blank', 'width=350,height=450');
    
    var htmlContent = 
        '<!DOCTYPE html>\n' +
        '<html lang="ar" dir="rtl">\n' +
        '<head>\n' +
        '    <title>طباعة ملصق</title>\n' +
        '    <style>\n' +
        '        @page {\n' +
        '            size: 30mm 40mm; /* حجم الملصق الفعلي */\n' +
        '            margin: 0 !important; /* إلغاء الهوامش تماماً لاستغلال كامل المساحة */\n' +
        '        }\n' +
        '        html, body {\n' +
        '            margin: 0 !important;\n' +
        '            padding: 0 !important;\n' +
        '            width: 30mm !important;\n' +
        '            height: 40mm !important;\n' +
        '            background-color: #fff;\n' +
        '            overflow: hidden !important;\n' +
        '            box-sizing: border-box !important;\n' +
        '            font-family: Arial, sans-serif;\n' +
        '            /* وضع التموضع المطلق على الـ body ليكون مرجعاً للعناصر بداخلها */\n' +
        '            position: relative !important;\n' +
        '        }\n' +
        '        /* حاوية الـ QR وتحديد موقعها وحساب توسطها الأفقي بدقة */\n' +
        '        .qr-container {\n' +
        '            width: 22mm !important; /* تكبير الـ QR للحجم الأنسب */\n' +
        '            height: 22mm !important;\n' +
        '            position: absolute !important;\n' +
        '            top: 2.5mm !important; /* مسافة من الأعلى */\n' +
        '            /* الحساب: (عرض الورقة 30mm - عرض الكود 22mm) / 2 = 4mm للـ left للتوسط تماماً */\n' +
        '            left: 4mm !important;\n' +
        '            display: block !important;\n' +
        '        }\n' +
        '        /* ضمان أن الـ SVG الداخلي يأخذ أبعاد الحاوية الصارمة */\n' +
        '        .qr-container svg {\n' +
        '            width: 22mm !important;\n' +
        '            height: 22mm !important;\n' +
        '            display: block !important;\n' +
        '        }\n' +
        '        /* حاوية تفاصيل البيانات وتحديد موقعها وتوسطها الأفقي */\n' +
        '        .asset-details {\n' +
        '            width: 27mm !important; /* تحديد عرض أقل من الورقة بقليل لترك مسافة أمان */\n' +
        '            position: absolute !important;\n' +
        '            top: 26mm !important; /* مسافة أمان بعد الـ QR */\n' +
        '            /* الحساب: (عرض الورقة 30mm - عرض الحاوية 27mm) / 2 = 1.5mm للـ left للتوسط */\n' +
        '            left: 1.5mm !important;\n' +
        '            display: flex !important;\n' +
        '            flex-direction: column !important;\n' +
        '            align-items: center !important;\n' +
        '            justify-content: flex-start !important;\n' +
        '            gap: 0.5mm !important; /* مسافة بسيطة بين الأسطر */\n' +
        '            text-align: center !important;\n' +
        '        }\n' +
        '        /* الأسطر النصية مع تكبير الخط ليكون مقروءاً بوضوح */\n' +
        '        .detail-line {\n' +
        '            font-size: 7.5pt !important; /* تكبير حجم الخط */\n' +
        '            color: #000 !important;\n' +
        '            margin: 0 !important;\n' +
        '            white-space: nowrap !important;\n' +
        '            overflow: hidden !important;\n' +
        '            text-overflow: ellipsis !important;\n' +
        '            width: 100% !important;\n' +
        '            line-height: 1.2 !important;\n' +
        '        }\n' +
        '        .asset-code {\n' +
        '            font-size: 8pt !important;\n' +
        '            font-weight: bold !important;\n' +
        '        }\n' +
        '        .asset-title {\n' +
        '            font-weight: bold !important;\n' +
        '        }\n' +
        '    </style>\n' +
        '</head>\n' +
        '<body>\n' +
        '    \n' +
        '    <div class="qr-container">' + qrHtml + '</div>\n' +
        '    <div class="asset-details">\n' +
        '        <div class="detail-line asset-code">ID: ' + (assetCode || '---') + '</div>\n' +
        '        <div class="detail-line asset-title">' + (assetName || '---') + '</div>\n' +
        '        <div class="detail-line">الشركة: ' + (companyName || '---') + '</div>\n' +
        '        <div class="detail-line">الموقع: ' + (locationName || '---') + '</div>\n' +
        '    </div>\n' +
        '    <script>\n' +
        '        window.onload = function() {\n' +
        '            window.print();\n' +
        '            setTimeout(function() { window.close(); }, 500);\n' +
        '        };\n' +
        '    <\/script>\n' +
        '</body>\n' +
        '</html>';

    printWindow.document.write(htmlContent);
    printWindow.document.close();
}
</script>
@endsection