@extends('layouts.admin')
@section('title', 'تاريخ حركات المصروفات التفصيلي')
@section('content')
<div class="container-fluid" style="direction: rtl; text-align: left; font-family: 'Cairo', sans-serif;">
    
    <div class="card card-info card-outline shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="font-weight-bold text-info mb-1">
                    <i class="fas fa-history ml-2"></i> كشف مصروفات: {{ $company->name }}
                </h4>
                <p class="text-muted mb-0">
                    استعراض الحركات المالية المخصصة لـ: 
                    <span class="badge badge-info p-1">
                        {{ \Carbon\Carbon::create()->month($selectedMonth)->translatedFormat('F') }} / {{ $selectedYear }}
                    </span>
                </p>
            </div>
            <div>
                <span class="h5 badge badge-danger p-3 shadow-sm">
                    إجمالي مصروفات الفترة المحددة: {{ number_format($monthly_total, 2) }} د.ل
                </span>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4 print-hidden">
        <div class="card-header bg-light">
            <h5 class="card-title text-dark font-weight-bold mb-0" style="font-size: 1rem;">
                <i class="fas fa-search ml-2 text-info"></i> محرك البحث المالي والفلترة المتقدمة
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('expenses.history', $company->id) }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold text-secondary">البحث الشامل:</label>
                        <input type="text" name="search" class="form-control" placeholder="رقم الفاتورة، البيان، الملاحظات..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold text-secondary">تغيير الشركة:</label>
                        <select name="company_route_id" class="form-control" onchange="if(this.value) window.location.href='/companies/expenses/history/' + this.value;">
                            @foreach(\App\Models\Company::all() as $comp)
                                <option value="{{ $comp->id }}" {{ $comp->id == $company->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="font-weight-bold text-secondary">الشهر:</label>
                        <select name="month" class="form-control">
                            @for ($m=1; $m<=12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="font-weight-bold text-secondary">السنة:</label>
                        <select name="year" class="form-control">
                            @for ($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-2 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-info btn-block font-weight-bold">
                            <i class="fas fa-filter ml-1"></i> تصفية
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title text-dark font-weight-bold mb-0">
                <i class="fas fa-file-invoice-dollar ml-2 text-secondary"></i> تفاصيل القيود الماليّة وحالات السندات
            </h3>
            <div class="card-tools">
                <button onclick="window.print();" class="btn btn-default btn-sm border text-dark font-weight-bold">
                    <i class="fas fa-print ml-1 text-primary"></i> طباعة كشف الشهر الحـالي
                </button>
                <a href="{{ route('expenses.create', ['company_id' => $company->id]) }}" class="btn btn-danger btn-sm font-weight-bold mr-1">
                    <i class="fas fa-plus ml-1"></i> إضافة حركة جديدة
                </a>
            </div>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-bordered text-center m-0">
                <thead class="bg-info text-white">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">تاريخ الصرف</th>
                        <th style="width: 15%">رقم الفاتورة / الكود</th>
                        <th style="width: 20%">بند المصروف</th>
                        <th style="width: 15%">المبلغ (دينار)</th>
                        <th style="width: 20%">البيان / ملاحظات الحركة</th>
                        <th style="width: 10%">الإجراءات والعمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @php $rowNum = 1; @endphp
                    
                    @foreach($expenses->groupBy(function($item) { return $item->transaction_code ?? uniqid(); }) as $trackCode => $group)
                        
                        {{-- الحالة الأولى: فاتورة مجمعة متعددة البنود --}}
                        @if($group->count() > 1)
                            <tr class="bg-light" style="border-left: 5px solid #17a2b8;">
                                <td class="align-middle">{{ $rowNum++ }}</td>
                                <td class="align-middle font-weight-bold">{{ $group->first()->expense_date }}</td>
                                <td class="align-middle">
                                    <span class="badge badge-dark mb-1">فاتورة مجمعة</span><br>
                                    <span class="text-info font-weight-bold">{{ $group->first()->transaction_code ?? 'لا يوجد' }}</span>
                                </td>
                                <td class="align-middle text-left">
                                    <ul class="mb-0 pr-3 text-muted" style="list-style-type: square; font-size: 0.9rem;">
                                        @foreach($group as $item)
                                            <li>
                                                <span class="badge badge-secondary py-1 px-2">{{ $item->category->name ?? 'بدون بند' }}</span> 
                                                : <strong class="text-dark">{{ number_format($item->amount, 2) }} د.ل</strong>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="align-middle font-weight-bold text-danger" style="font-size: 1.1rem;">
                                    {{ number_format($group->sum('amount'), 2) }} د.ل
                                </td>
                                <td class="align-middle text-left text-secondary" style="font-size: 0.9rem;">
                                    {{ $group->pluck('notes')->filter()->implode(' | ') ?: 'لا توجد ملاحظات إضافية' }}
                                </td>
                                <td class="align-middle">
                                    {{-- معاينة المرفق المجمع إن وجد --}}
                                    @if($group->first()->document_path)
                                        <button type="button" class="btn btn-sm btn-outline-info" title="عرض الفاتورة أو الإيصال" 
                                                onclick="viewInvoiceDetail('{{ $group->first()->transaction_code }}', '{{ $group->first()->expense_date }}', 'فاتورة مجمعة', '{{ number_format($group->sum('amount'), 2) }}', '{{ $group->pluck('notes')->filter()->implode(' | ') }}', '{{ asset($group->first()->document_path) }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif

                                    {{-- زر طباعة السند المنفرد --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="طباعة السند"
                                            onclick="printSingleInvoice('{{ $company->name }}', '{{ $group->first()->transaction_code }}', '{{ $group->first()->expense_date }}', 'فاتورة مجمعة', '{{ number_format($group->sum('amount'), 2) }}', '{{ $group->pluck('notes')->filter()->implode(' | ') }}')">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    
                                    <form action="{{ route('expenses.destroy', $group->first()->id) }}" method="POST" class="d-inline" onclick="return confirm('تنبيه: أنت تقوم بحذف فاتورة مجمعة، هل أنت متأكد من حذف هذه الحركة بكافة بنودها؟')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="delete_group_code" value="{{ $group->first()->transaction_code }}">
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف الفاتورة بالكامل">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @else
                            {{-- الحالة الثانية: فاتورة عادية من سطر واحد --}}
                            @php $item = $group->first(); @endphp
                            <tr>
                                <td class="align-middle">{{ $rowNum++ }}</td>
                                <td class="align-middle">{{ $item->expense_date }}</td>
                                {{-- تم تعديل الاستدعاء هنا إلى transaction_code ليتوافق مع قاعدة بياناتك --}}
                                <td class="align-middle text-secondary font-weight-bold">{{ $item->transaction_code ?? 'N/A' }}</td>
                                <td class="align-middle"><span class="badge badge-pill badge-secondary p-2" style="font-size: 0.85rem;">{{ $item->category->name ?? 'بدون بند' }}</span></td>
                                <td class="align-middle font-weight-bold text-dark">{{ number_format($item->amount, 2) }} د.ل</td>
                                <td class="align-middle text-left text-muted" style="font-size: 0.9rem;">{{ $item->notes ?? 'لا توجد ملاحظات مسجلة' }}</td>
                                <td class="align-middle">
                                    {{-- زر المعاينة عبر المودال للفاتورة المفردة --}}
                                    @if($item->document_path)
                                        <button type="button" class="btn btn-sm btn-outline-info" title="عرض المرفق"
                                                onclick="viewInvoiceDetail('{{ $item->transaction_code ?? 'N/A' }}', '{{ $item->expense_date }}', '{{ $item->category->name ?? 'عام' }}', '{{ number_format($item->amount, 2) }}', '{{ $item->notes ?? 'لا توجد' }}', '{{ asset($item->document_path) }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif

                                    {{-- زر طباعة الإيصال المنفرد للفاتورة المفردة --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="طباعة الإيصال"
                                            onclick="printSingleInvoice('{{ $company->name }}', '{{ $item->transaction_code ?? 'N/A' }}', '{{ $item->expense_date }}', '{{ $item->category->name ?? 'عام' }}', '{{ number_format($item->amount, 2) }}', '{{ $item->notes ?? 'لا توجد' }}')">
                                        <i class="fas fa-print"></i>
                                    </button>

                                    <form action="{{ route('expenses.destroy', $item->id) }}" method="POST" class="d-inline" onclick="return confirm('هل أنت متأكد من حذف هذا السجل المالي؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    @if($expenses->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle mr-1"></i> لا توجد أي حركات مصروفات مسجلة لهذه الشركة خلال الفترة المحددة.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- مودال المعاينة المطور --}}
<div class="modal fade" id="viewInvoiceModal" tabindex="-1" role="dialog" aria-hidden="true" style="direction: rtl; text-align: left;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-file-invoice ml-2"></i> تفاصيل ومرفقات السند المالي</h5>
                <button type="button" class="close text-white ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <div class="row mb-3">
                    <div class="col-6"><strong>رقم الفاتورة/الكود:</strong> <span id="m_inv_num"></span></div>
                    <div class="col-6"><strong>تاريخ الحركة:</strong> <span id="m_inv_date"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><strong>بند المصروف:</strong> <span id="m_inv_cat" class="badge badge-secondary p-2"></span></div>
                    <div class="col-6"><strong>القيمة المالية:</strong> <span id="m_inv_amount" class="text-danger font-weight-bold"></span> د.ل</div>
                </div>
                <div class="mb-3">
                    <strong>البيان والبيانات التفصيلية:</strong>
                    <div id="m_inv_notes" class="p-2 border bg-white rounded mt-1"></div>
                </div>
                <div class="text-center mt-3">
                    <strong>مرفق السند الإيصالي:</strong>
                    <div id="m_inv_file_container" class="mt-2"></div>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal">إغلاق المعاينة</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewInvoiceDetail(invNum, invDate, invCat, invAmount, invNotes, filePath) {
    document.getElementById('m_inv_num').innerText = invNum;
    document.getElementById('m_inv_date').innerText = invDate;
    document.getElementById('m_inv_cat').innerText = invCat;
    document.getElementById('m_inv_amount').innerText = invAmount;
    document.getElementById('m_inv_notes').innerText = invNotes;
    
    var fileContainer = document.getElementById('m_inv_file_container');
    fileContainer.innerHTML = '';
    
    if (filePath && filePath.trim() !== '' && !filePath.includes('null')) {
        var ext = filePath.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            fileContainer.innerHTML = '<img src="'+filePath+'" class="img-fluid rounded border shadow-sm" style="max-height: 400px; object-fit: contain;">';
        } else {
            fileContainer.innerHTML = '<a href="'+filePath+'" target="_blank" class="btn btn-outline-info btn-block"><i class="fas fa-file-download ml-1"></i> تحميل أو استعراض المرفق المالي</a>';
        }
    } else {
        fileContainer.innerHTML = '<span class="text-muted small"><i class="fas fa-exclamation-triangle ml-1"></i> لا يوجد ملف مرفق أو فاتورة مصورة لهذا السند.</span>';
    }
    
    $('#viewInvoiceModal').modal('show');
}

function printSingleInvoice(companyName, invNum, invDate, invCat, invAmount, invNotes) {
    var printWindow = window.open('', '_blank', 'height=600,width=800');
    printWindow.document.write('<html><head><title>طباعة فاتورة مصروف منفردة</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">');
    printWindow.document.write('<style>body{direction:rtl; text-align:right; font-family:"Cairo", sans-serif; padding:40px;} .ticket-box{border:2px dashed #000; padding:20px; border-radius:5px;} .title{text-align:center; font-weight:bold; margin-bottom:20px;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<div class="ticket-box">');
    printWindow.document.write('<h3 class="title">إيصال صرف مالي تشغيلي منفرد</h3>');
    printWindow.document.write('<h5 class="text-center font-weight-bold text-primary">' + companyName + '</h5><hr>');
    printWindow.document.write('<p><strong>رقم السند/الفاتورة:</strong> ' + invNum + '</p>');
    printWindow.document.write('<p><strong>تاريخ حركـة الصرف:</strong> ' + invDate + '</p>');
    printWindow.document.write('<p><strong>بند المصروف المعتمد:</strong> ' + invCat + '</p>');
    printWindow.document.write('<p><strong>المبلغ المالي المصرُوف:</strong> <span class="text-danger font-weight-bold">' + invAmount + ' د.ل</span></p>');
    printWindow.document.write('<p><strong>البيان والملحوظات:</strong> ' + invNotes + '</p>');
    printWindow.document.write('<hr><div class="d-flex justify-content-between mt-5"><p>توقيع المحاسب المسئول: ............</p><p>تاريخ الطباعة المعتمد: ' + new Date().toLocaleDateString('ar-LY') + '</p></div>');
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function() { printWindow.print(); printWindow.close(); }, 700);
}
</script>

<style>
    @media print {
        .print-hidden, .main-sidebar, .main-header, .main-footer, .btn, .card-tools, .modal {
            display: none !important;
        }
        .content-wrapper {
            margin-right: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection