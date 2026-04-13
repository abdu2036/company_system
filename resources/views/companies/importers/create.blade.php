@extends('layouts.admin')

@section('title', 'إضافة سجل مستورد')

@section('content_header')
<h1 class="text-right">إضافة سجل مستورد جديد 🚢</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-right">
        <h3 class="card-title float-right">بيانات سجل المستوردين 📝</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('importers.store') }}" method="POST">
            @csrf
            <div class="row text-right" style="direction: rtl;">

                {{-- اختيار الشركة - يظهر فقط الشركات التي ليس لها سجل --}}
                <div class="form-group col-md-12">
                    <label>اختيار الشركة <span class="text-danger">*</span></label>
<select name="company_id" class="form-control">
    <option value="">-- اختر الشركة --</option>
    @foreach($companies as $company)
        <option value="{{ $company->id }}">{{ $company->name }}</option>
    @endforeach
</select>
                </div>

                {{-- رقم السجل --}}
                <div class="form-group col-md-4">
                    <label>رقم السجل 🔢 <span class="text-danger">*</span></label>
                    <input type="text" name="importer_number" class="form-control" placeholder="أدخل رقم السجل" required>
                </div>

                {{-- تاريخ الإصدار --}}
                <div class="form-group col-md-4">
                    <label>تاريخ الإصدار 📅 <span class="text-danger">*</span></label>
                    <input type="date" name="issue_date" id="issue_date" class="form-control" required>
                </div>

                {{-- مدة الصلاحية --}}
                <div class="form-group col-md-2">
                    <label>مدة الصلاحية ⏳</label>
                    <select id="validity_period" class="form-control">
                        <option value="1">سنة واحدة</option>
                        <option value="2">سنتين</option>
                        <option value="3">3 سنوات</option>
                    </select>
                </div>

                {{-- تاريخ الانتهاء --}}
                <div class="form-group col-md-2">
                    <label>تاريخ الانتهاء 🏁</label>
                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" required readonly>
                </div>

                {{-- رفع الملف --}}
                <div class="form-group col-md-12">
                    <label>إرفاق سجل المستوردين (PDF/Image) 📁</label>
                    <input type="file" class="form-control" onchange="uploadFile(this, 'importer')">
                    
                    <div id="upload_status" class="mt-2"></div>
                    <input type="hidden" name="temp_file_path" id="temp_file_path">
                </div>
            </div>

            <div class="card-footer text-left mt-3">
                <button type="submit" class="btn btn-info shadow-sm font-weight-bold">حفظ بيانات السجل</button>
                <a href="{{ route('importers.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function () {
    const issueDateInput = document.getElementById('issue_date');
    const validitySelect = document.getElementById('validity_period');
    const expiryDateInput = document.getElementById('expiry_date');

    // دالة تحديث تاريخ الانتهاء تلقائياً
    function updateExpiryDate() {
        const issueDateValue = issueDateInput.value;
        const yearsToAdd = parseInt(validitySelect.value);

        if (issueDateValue && yearsToAdd) {
            const date = new Date(issueDateValue);
            date.setFullYear(date.getFullYear() + yearsToAdd);
            
            const yyyy = date.getFullYear();
            let mm = date.getMonth() + 1;
            let dd = date.getDate();

            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;

            expiryDateInput.value = yyyy + '-' + mm + '-' + dd;
        }
    }

    issueDateInput.addEventListener('change', updateExpiryDate);
    validitySelect.addEventListener('change', updateExpiryDate);

    // دالة الرفع الذكي AJAX
    window.uploadFile = function (input, type) {
        let file = input.files[0];
        if (!file) return;

        let formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);

        let statusDiv = document.getElementById('upload_status');
        statusDiv.innerHTML = '<span class="text-info">جاري الرفع... ⏳</span>';

        fetch('/upload-temp', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                statusDiv.innerHTML = '<span class="text-success font-weight-bold">✅ تم رفع الملف بنجاح</span>';
                document.getElementById('temp_file_path').value = data.temp_path;
            } else {
                statusDiv.innerHTML = '<span class="text-danger">❌ فشل الرفع</span>';
            }
        })
        .catch(error => {
            statusDiv.innerHTML = '<span class="text-danger">❌ خطأ في السيرفر</span>';
        });
    };
});
</script>
@stop