@extends('layouts.admin')

@section('title', 'تجديد الترخيص')

@section('content_header')
<h1 class="text-right">تجديد ترخيص الشركة 📜</h1>
@stop

@section('content')
<div class="container-fluid" dir="rtl">
    <div class="card shadow-sm border-warning">
        <div class="card-header bg-warning text-right">
            <h3 class="card-title float-right text-dark font-weight-bold">
                تجديد بيانات الترخيص لشركة: <span class="text-primary">{{ $license->company->name }}</span>
            </h3>
        </div>
        <form action="{{ route('licenses.renewUpdate', $license->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                {{-- بيانات الشركة ورقم الترخيص الحالي (مظلل) --}}
                <div class="row mb-4 bg-light p-3 rounded border shadow-sm">
                    <div class="col-md-6 text-right">
                        <label class="font-weight-bold text-dark">اسم الشركة</label>
                        <input type="text" class="form-control text-right" value="{{ $license->company->name }}" readonly 
                               style="background-color: #e9ecef; border: 1px solid #ced4da; cursor: not-allowed; opacity: 1;">
                    </div>
                    <div class="col-md-6 text-right">
                        <label class="font-weight-bold text-dark">رقم الترخيص الحالي</label>
                        <input type="text" class="form-control text-right" value="{{ $license->license_number }}" readonly 
                               style="background-color: #e9ecef; border: 1px solid #ced4da; cursor: not-allowed; opacity: 1;">
                    </div>
                </div>

                <h5 class="text-right mb-3 text-secondary border-bottom pb-2">تفاصيل التجديد الجديد</h5>

                <div class="row">
                    <div class="col-md-4 text-right">
                        <label>تاريخ التجديد (الإصدار) *</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control js-date-issue text-right" required>
                    </div>

                    <div class="col-md-4 text-right">
                        <label>مدة التجديد *</label>
                        <select id="validity_period" name="validity_period" class="form-control js-date-validity">
                            <option value="1">سنة واحدة</option>
                            <option value="2">سنتين</option>
                            <option value="3">ثلاث سنوات</option>
                            <option value="5">خمس سنوات</option>
                        </select>
                    </div>

                    <div class="col-md-4 text-right">
                        <label>تاريخ الانتهاء الجديد *</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control js-date-expiry text-right" 
                               readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12 text-right">
                        <label>إرفاق صورة الترخيص الجديد (PDF)</label>
                        <input type="file" class="form-control-file" onchange="uploadFile(this, 'license')">
                        <input type="hidden" name="temp_file_path" id="temp_file_path">
                        <div id="upload_status" class="mt-2"></div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-left">
                <button type="submit" class="btn btn-warning px-5 font-weight-bold">تأكيد التجديد ✅</button>
                <a href="{{ route('licenses.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
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
        $('#issue_date, #validity_period').on('change', calculateExpiry);

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
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            }).then(response => response.json()).then(data => {
                if (data.status === 'success') {
                    statusDiv.innerHTML = '<span class="text-success font-weight-bold">✅ تم الرفع بنجاح</span>';
                    document.getElementById('temp_file_path').value = data.temp_path;
                } else {
                    statusDiv.innerHTML = '<span class="text-danger">❌ فشل الرفع</span>';
                }
            }).catch(error => {
                statusDiv.innerHTML = '<span class="text-danger">❌ خطأ في الاتصال</span>';
            });
        };
    });
</script>
@stop