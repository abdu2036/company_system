@extends('layouts.admin')
@section('title', 'تسجيل حركة إيراد جديدة للشركة - ' . $company->name)
@section('content')
<div class="container-fluid" style="direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
    <div class="card card-success card-outline shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center flex-row-reverse">
            <h3 class="card-title float-right mb-0">
                <i class="fas fa-hand-holding-usd ml-2 text-success"></i>
                تسجيل حركة إيراد مالي مخصص لشركة: <span class="text-primary">{{ $company->name }}</span>
            </h3>
            <button type="button" class="btn btn-success btn-sm font-weight-bold ml-auto mr-0" id="add-row-btn">
                <i class="fas fa-plus ml-1"></i> إضافة سطر آخر +
            </button>
        </div>

        <form action="{{ route('revenues.store_multiple') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="company_id" value="{{ $company->id }}">

            <div class="card-body">
                <div class="mb-3 text-right">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-toggle="modal" data-target="#addCategoryModal">
                        <i class="fas fa-tags ml-1"></i> إدارة بنود الإيرادات +
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center" id="revenues-table">
                        <thead class="bg-success text-white">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">بند الإيراد <span class="text-warning">*</span></th>
                                <th style="width: 15%">طريقة القبض <span class="text-warning">*</span></th>
                                <th style="width: 12%">تاريخ القبض <span class="text-warning">*</span></th>
                                <th style="width: 15%">المبلغ المالي (د.ل) <span class="text-warning">*</span></th>
                                <th style="width: 28%">البيان / ملاحظات تفصيلية</th>
                                <th style="width: 5%">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="revenues-tbody">
                            <tr class="revenue-row">
                                <td class="row-number">1</td>
                                <td>
                                    <select name="categories[0]" class="form-control text-right" required>
                                        <option value="">-- اختر البند المخصص --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="payment_methods[0]" class="form-control text-right font-weight-bold" required>
                                        <option value="cash" class="text-success">💵 نقدي (خزينة)</option>
                                        <option value="bank" class="text-primary">🏦 تحويل مصرفي (البنك)</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" name="revenue_date" class="form-control text-center" value="{{ date('Y-m-d') }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="amounts[0]" class="form-control price-input text-center font-weight-bold" placeholder="0.00" required>
                                </td>
                                <td>
                                    <input type="text" name="notes[0]" class="form-control text-right" placeholder="اكتب تفاصيل حركة الإيراد أو القبض هنا...">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-secondary disabled" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>
                <div class="row mt-4 justify-content-start">
                    <div class="col-md-5">
                        <div class="info-box bg-light border shadow-sm">
                            <div class="info-box-content text-dark">
                                <label class="font-weight-bold text-secondary d-block">
                                    <i class="fas fa-paperclip ml-1"></i>
                                    إرفاق إيصال القبض الكلي أو السند المالي (اختياري):
                                </label>

                                <label for="file_upload_input" class="btn btn-success font-weight-bold mb-2">
                                    <i class="fas fa-folder-open ml-1"></i>
                                    اختيار ملف
                                </label>

                                <input
                                    type="text"
                                    id="file_name_display"
                                    class="form-control bg-white text-right"
                                    placeholder="لم يتم اختيار ملف"
                                    readonly
                                >

                                <input
                                    type="file"
                                    id="file_upload_input"
                                    name="document"
                                    class="d-none"
                                    onchange="updateFileNameDisplay(this)"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                >

                                <small class="text-muted mt-2 d-block">
                                    عند الضغط على "اختيار ملف" سيفتح جهازك مباشرة لإرفاق السند أو الشيك المالي.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-left">
                <button type="submit" class="btn btn-success px-5 font-weight-bold">
                    <i class="fas fa-save ml-1"></i> اعتماد وتسجيل الإيرادات بالكامل
                </button>
                <a href="{{ route('revenues.history', $company->id) }}" class="btn btn-secondary px-5">إلغاء</a>
            </div>
        </form>
    </div>
</div>

@include('companies.revenues.categories')

<script>
document.getElementById('add-row-btn').addEventListener('click', function() {
    const tbody = document.getElementById('revenues-tbody');
    const rowCount = tbody.getElementsByClassName('revenue-row').length;
    
    // إنشاء السطر الجديد ديناميكياً باستخدام الـ Index المحدث المتوافق مع الـ Controller
    const newRow = document.createElement('tr');
    newRow.className = 'revenue-row';
    newRow.innerHTML = `
        <td class="row-number">${rowCount + 1}</td>
        <td>
            <select name="categories[${rowCount}]" class="form-control text-right" required>
                <option value="">-- اختر البند المخصص --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="payment_methods[${rowCount}]" class="form-control text-right font-weight-bold" required>
                <option value="cash" class="text-success">💵 نقدي (خزينة)</option>
                <option value="bank" class="text-primary">🏦 تحويل مصرفي (البنك)</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control text-center text-muted" value="نفس التاريخ المعتمد" readonly disabled>
        </td>
        <td>
            <input type="number" step="0.01" name="amounts[${rowCount}]" class="form-control price-input text-center font-weight-bold" placeholder="0.00" required>
        </td>
        <td>
            <input type="text" name="notes[${rowCount}]" class="form-control text-right" placeholder="اكتب تفاصيل حركة الإيراد أو القبض هنا...">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-row-btn" title="حذف هذا السطر">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    
    // تفعيل حدث الحذف للزر المضاف حديثاً
    attachRemoveEvent(newRow.querySelector('.remove-row-btn'));
});

// دالة ربط حدث الحذف بالسطر الحالي
function attachRemoveEvent(button) {
    button.addEventListener('click', function() {
        const row = this.closest('.revenue-row');
        row.remove();
        recalculateRowNumbers();
    });
}

// إعادة هيكلة الـ Array هندسياً لمنع حدوث أي نقص أو فجوات أثناء استقبال المدخلات في مصفوفة الـ Backend
function recalculateRowNumbers() {
    const rows = document.getElementById('revenues-tbody').getElementsByClassName('revenue-row');
    Array.from(rows).forEach((row, index) => {
        row.querySelector('.row-number').innerText = index + 1;
        
        // تعديل كود الـ Name البرمجي ليصبح متسلسلاً تصاعدياً: [0], [1], [2]...
        const selectCat = row.querySelector('select[name^="categories["]');
        if (selectCat) selectCat.name = `categories[${index}]`;

        const selectMethod = row.querySelector('select[name^="payment_methods["]');
        if (selectMethod) selectMethod.name = `payment_methods[${index}]`;

        const inputAmount = row.querySelector('input[name^="amounts["]');
        if (inputAmount) inputAmount.name = `amounts[${index}]`;

        const inputNotes = row.querySelector('input[name^="notes["]');
        if (inputNotes) inputNotes.name = `notes[${index}]`;
    });
}

function updateFileNameDisplay(input) {
    if (input.files && input.files.length > 0) {
        var filename = input.files[0].name;
        document.getElementById('file_name_display').value = filename;
    }
}
</script>
@endsection