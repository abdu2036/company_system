@extends('layouts.admin')

@section('title', 'تجديد سجل المستوردين')

@section('content')
<style>
    /* حل نهائي لمشكلة اليسار في هذه الصفحة */
    .renew-page-container {
        direction: rtl !important;
        text-align: right !important;
    }
    .renew-page-container label {
        display: block !important;
        width: 100% !important;
        text-align: right !important;
    }
    .renew-page-container .input-group, .renew-page-container .form-control {
        direction: rtl !important;
        text-align: right !important;
    }
    /* لضمان بقاء العنوان في اليمين داخل الهيدر */
    .card-header .card-title {
        float: right !important;
    }
</style>

<div class="container-fluid renew-page-container">
    <div class="card shadow-sm border-warning">
        <div class="card-header bg-warning">
            <h3 class="card-title font-weight-bold text-dark">
                تجديد بيانات سجل المستوردين لشركة: <span class="text-primary">{{ $importer->company->name }}</span>
            </h3>
        </div>

        <form action="{{ route('importers.update_renew', $importer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                {{-- معلومات العرض --}}
                <div class="row mb-4 bg-light p-3 rounded border shadow-sm">
                    <div class="col-md-6">
                        <label class="font-weight-bold text-dark">اسم الشركة 🏢</label>
                        <input type="text" class="form-control" value="{{ $importer->company->name }}" readonly style="background-color: #e9ecef;">
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold text-dark">رقم السجل الحالي 🔢</label>
                        <input type="text" class="form-control" value="{{ $importer->importer_number }}" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                <h5 class="mb-3 text-secondary border-bottom pb-2">تفاصيل التجديد الجديد</h5>

                <div class="row">
                    <div class="col-md-4">
                        <label>تاريخ التجديد (الإصدار) * 📅</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>مدة التجديد * ⏳</label>
                        <select id="validity_period" class="form-control">
                            <option value="1">سنة واحدة</option>
                            <option value="2">سنتين</option>
                            <option value="3">ثلاث سنوات</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>تاريخ الانتهاء الجديد * 🏁</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <label>إرفاق السجل الجديد (PDF) 📁</label>
                        <input type="file" class="form-control-file" onchange="uploadFile(this, 'importer')">
                        <input type="hidden" name="temp_file_path" id="temp_file_path">
                        <div id="upload_status" class="mt-2"></div>
                    </div>
                </div>
                
                <input type="hidden" name="importer_number" value="{{ $importer->importer_number }}">
            </div>

            <div class="card-footer text-left">
                <button type="submit" class="btn btn-warning px-5 font-weight-bold">تأكيد التجديد ✅</button>
                <a href="{{ route('importers.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // دالة حساب التاريخ التلقائي بناءً على المدة المختارة
        function calculateExpiry() {
            let issueDateValue = $('#issue_date').val();
            let yearsToAdd = parseInt($('#validity_period').val());

            if (issueDateValue && yearsToAdd) {
                let date = new Date(issueDateValue);
                date.setFullYear(date.getFullYear() + yearsToAdd);

                let y = date.getFullYear();
                let m = String(date.getMonth() + 1).padStart(2, '0');
                let d = String(date.getDate()).padStart(2, '0');

                $('#expiry_date').val(`${y}-${m}-${d}`);
            }
        }

        // تنفيذ الحساب عند تغيير التاريخ أو المدة
        $('#issue_date, #validity_period').on('change', calculateExpiry);

        // نظام رفع الملفات AJAX
        window.uploadFile = function (input, type) {
            let file = input.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append('file', file);
            formData.append('type', type);

            let statusDiv = document.getElementById('upload_status');
            statusDiv.innerHTML = '<span class="text-info">جاري رفع الملف الجديد... ⏳</span>';

            fetch('/upload-temp', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        statusDiv.innerHTML = '<span class="text-success font-weight-bold">✅ تم تجهيز السجل الجديد للرفع</span>';
                        document.getElementById('temp_file_path').value = data.temp_path;
                    } else {
                        statusDiv.innerHTML = '<span class="text-danger">❌ فشل الرفع، حاول ثانية</span>';
                    }
                })
                .catch(error => {
                    statusDiv.innerHTML = '<span class="text-danger">❌ خطأ في الاتصال بالسيرفر</span>';
                });
        };
    });
</script>
@stop