@extends('layouts.admin')
@section('title', 'تسجيل مصروف مالي جديد للشركة - ' . $company->name)
@section('content')
<div class="container-fluid" style="direction: rtl; text-align: left; font-family: 'Cairo', sans-serif;">
    <div class="card card-danger card-outline shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center flex-row-reverse">
            <h3 class="card-title float-left mb-0">
                <i class="fas fa-hand-holding-usd ml-2 text-danger"></i>
                تسجيل مصروف مالي مخصص لشركة: <span class="text-primary">{{ $company->name }}</span>
            </h3>
            <button type="button" class="btn btn-success btn-sm font-weight-bold ml-auto mr-0" id="add-row-btn">
                <i class="fas fa-plus ml-1"></i> إضافة سطر آخر +
            </button>
        </div>

        <form action="{{ route('expenses.store_multiple') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="company_id" value="{{ $company->id }}">

            <div class="card-body">
                <div class="mb-3 text-left">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-toggle="modal" data-target="#addCategoryModal">
                        <i class="fas fa-tags ml-1"></i> إدارة بنود المصروفات +
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center" id="expenses-table">
                        <thead class="bg-danger text-white">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 22%">بند المصروف <span class="text-warning">*</span></th>
                                <th style="width: 15%">رقم الفاتورة</th>
                                <th style="width: 15%">تاريخ الصرف <span class="text-warning">*</span></th>
                                <th style="width: 15%">المبلغ المالي (د.ل) <span class="text-warning">*</span></th>
                                <th style="width: 23%">البيان / ملاحظات تفصيلية</th>
                                <th style="width: 5%">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="expenses-tbody">
                            <tr class="expense-row">
                                <td class="row-number">1</td>
                                <td>
                                    <select name="expenses[0][category_id]" class="form-control text-left" required>
                                        <option value="">-- اختر البند المخصص --</option>
                                        @foreach($company->expenseCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="expenses[0][invoice_number]" class="form-control text-center font-weight-bold text-secondary" placeholder="مثال: INV-1024">
                                </td>
                                <td>
                                    <input type="date" name="expenses[0][expense_date]" class="form-control text-center" value="{{ date('Y-m-d') }}" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="expenses[0][amount]" class="form-control price-input text-center font-weight-bold" placeholder="0.00" required>
                                </td>
                                <td>
                                    <input type="text" name="expenses[0][notes]" class="form-control text-left" placeholder="اكتب تفاصيل حركة الصرف هنا...">
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
                                <label class="font-weight-bold text-secondary"><i class="fas fa-paperclip ml-1"></i> إرفاق إيصال الدفع الكلي أو الفاتورة (اختياري):</label>
                                
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-danger font-weight-bold" onclick="document.getElementById('file_upload_input').click();">
                                            <i class="fas fa-folder-open ml-1"></i> اختيار ملف
                                        </button>
                                    </div>
                                    <input type="text" id="file_name_display" class="form-control bg-white text-left" placeholder="مسار ملف الفاتورة المرفوع..." readonly style="cursor: pointer;" onclick="document.getElementById('file_upload_input').click();">
                                </div>
                                
                                <input type="file" id="file_upload_input" name="expense_document" class="d-none" onchange="updateFileNameDisplay(this)">
                                <input type="hidden" name="temp_file_path" id="temp_file_path_input">
                                
                                <small class="text-muted mt-2 d-block">عند الضغط على "اختيار ملف" سيفتح جهازك مباشرة لإرفاق السند المالي.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-left">
                <button type="submit" class="btn btn-danger px-5 font-weight-bold">
                    <i class="fas fa-save ml-1"></i> اعتماد وتسجيل المصروفات بالكامل
                </button>
                <a href="{{ route('expenses.history', $company->id) }}" class="btn btn-secondary px-5">إلغاء</a>
            </div>
        </form>
    </div>
</div>

@include('companies.expenses.categories')

<script>
document.getElementById('add-row-btn').addEventListener('click', function() {
    const tbody = document.getElementById('expenses-tbody');
    const rowCount = tbody.getElementsByClassName('expense-row').length;
    
    // إنشاء السطر الجديد ديناميكياً باستخدام الـ Index التصاعدي التالي
    const newRow = document.createElement('tr');
    newRow.className = 'expense-row';
    newRow.innerHTML = `
        <td class="row-number">${rowCount + 1}</td>
        <td>
            <select name="expenses[${rowCount}][category_id]" class="form-control text-left" required>
                <option value="">-- اختر البند المخصص --</option>
                @foreach($company->expenseCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" name="expenses[${rowCount}][invoice_number]" class="form-control text-center font-weight-bold text-secondary" placeholder="مثال: INV-1024">
        </td>
        <td>
            <input type="date" name="expenses[${rowCount}][expense_date]" class="form-control text-center" value="{{ date('Y-m-d') }}" required>
        </td>
        <td>
            <input type="number" step="0.01" name="expenses[${rowCount}][amount]" class="form-control price-input text-center font-weight-bold" placeholder="0.00" required>
        </td>
        <td>
            <input type="text" name="expenses[${rowCount}][notes]" class="form-control text-left" placeholder="اكتب تفاصيل حركة الصرف هنا...">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-row-btn" title="حذف هذا السطر">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    
    // تفعيل حدث الحذف للزر الجديد
    attachRemoveEvent(newRow.querySelector('.remove-row-btn'));
});

// دالة ربط حدث الحذف بالسطر
function attachRemoveEvent(button) {
    button.addEventListener('click', function() {
        const row = this.closest('.expense-row');
        row.remove();
        recalculateRowNumbers();
    });
}

// إعادة تدوير الحقول وترقيمها هندسياً لضمان عدم حدوث فجوات أو تداخل أثناء الاستقبال بالـ Backend
function recalculateRowNumbers() {
    const rows = document.getElementById('expenses-tbody').getElementsByClassName('expense-row');
    Array.from(rows).forEach((row, index) => {
        row.querySelector('.row-number').innerText = index + 1;
        
        // تعديل الأسماء برمجياً لتصبح متسلسلة [0], [1], [2]...
        row.querySelector('select[name*="[category_id]"]').name = `expenses[${index}][category_id]`;
        row.querySelector('input[name*="[invoice_number]"]').name = `expenses[${index}][invoice_number]`;
        row.querySelector('input[name*="[expense_date]"]').name = `expenses[${index}][expense_date]`;
        row.querySelector('input[name*="[amount]"]').name = `expenses[${index}][amount]`;
        row.querySelector('input[name*="[notes]"]').name = `expenses[${index}][notes]`;
    });
}

function updateFileNameDisplay(input) {
    if (input.files && input.files.length > 0) {
        var filename = input.files[0].name;
        document.getElementById('file_name_display').value = filename;
        document.getElementById('temp_file_path_input').value = filename;
    }
}
</script>
@endsection