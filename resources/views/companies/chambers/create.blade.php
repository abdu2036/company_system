@extends('layouts.admin')

@section('title', 'إضافة اشتراك الغرفة التجارية')

@section('content_header')
<h1 class="text-right">إضافة اشتراك جديد للغرفة التجارية 🏢</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-right">
        <h3 class="card-title float-right">بيانات اشتراك الغرفة التجارية</h3>
    </div>

    <div class="card-body">

        <form action="{{ route('chambers.store') }}" method="POST">
            @csrf
            <div class="row text-right" style="direction: rtl;">

                {{-- اختيار الشركة --}}
                <div class="form-group col-md-12">
                    <label>اختيار الشركة <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-control select2" required>
                        <option value="">-- اختر الشركة --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- رقم العضوية --}}
                <div class="form-group col-md-3">
                    <label>رقم العضوية <span class="text-danger">*</span></label>
                    <input type="text" name="chamber_number" class="form-control" required>
                </div>

                
                    <div class="col-md-3">
                        <label>تاريخ الاشتراك *</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label>مدة صلاحية الغرفة *</label>
                        <select id="validity_period" class="form-control">
                            <option value="">-- اختر المدة --</option>
                            <option value="1">سنة واحدة</option>
                            <option value="2">سنتين</option>
                            <option value="3">ثلاث سنوات</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>تاريخ الانتهاء *</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control" required readonly>
                    </div>
                </div>

                {{-- رفع الشهادة --}}
                <div class="form-group col-md-12">
                    <label>إرفاق شهادة الغرفة التجارية (PDF/Image)</label>
                    <input type="file" class="form-control" onchange="uploadFile(this, 'chamber')">

                    <div id="upload_status" class="mt-2"></div>
                    <input type="hidden" name="temp_file_path" id="temp_file_path">
                </div>
            </div>

            <div class="card-footer text-left mt-3">
                <button type="submit" class="btn btn-info shadow-sm">حفظ البيانات</button>
                <a href="{{ route('chambers.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // 1. تم تعديل المستهدف هنا ليصبح #issue_date بدلاً من القديم
        $('#issue_date').on('change', function () {
            let subDate = new Date($(this).val());
            if (!isNaN(subDate.getTime())) {
                subDate.setFullYear(subDate.getFullYear() + 1);
                document.getElementById('expiry_date').value = subDate.toISOString().split('T')[0];
            }
        });

        // 2. وظيفة الرفع الذكي AJAX
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
                        statusDiv.innerHTML = '<span class="text-success font-weight-bold">✅ تم رفع الشهادة بنجاح</span>';
                        document.getElementById('temp_file_path').value = data.temp_path;
                    } else {
                        statusDiv.innerHTML = '<span class="text-danger font-weight-bold">❌ فشل الرفع</span>';
                    }
                })
                .catch(error => {
                    statusDiv.innerHTML = '<span class="text-danger font-weight-bold">❌ خطأ في السيرفر</span>';
                });
        };
    });
</script>
@stop
<script>
document.addEventListener('DOMContentLoaded', function() {
    const issueDateInput = document.getElementById('issue_date');
    const validitySelect = document.getElementById('validity_period');
    const expiryDateInput = document.getElementById('expiry_date');

    function updateExpiryDate() {
        const issueDateValue = issueDateInput.value;
        const yearsToAdd = parseInt(validitySelect.value);

        if (issueDateValue && yearsToAdd) {
            const date = new Date(issueDateValue);
            
            // إضافة عدد السنوات المختار
            date.setFullYear(date.getFullYear() + yearsToAdd);
            
            // طرح يوم واحد ليكون الانتهاء في اليوم السابق (اختياري حسب نظامك)
            // date.setDate(date.getDate() - 1);

            // تحويل التاريخ لصيغة YYYY-MM-DD ليتوافق مع حقل التاريخ
            const yyyy = date.getFullYear();
            let mm = date.getMonth() + 1; // الأشهر تبدأ من 0
            let dd = date.getDate();

            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;

            expiryDateInput.value = yyyy + '-' + mm + '-' + dd;
        }
    }

    // تشغيل الدالة عند تغيير أي من الحقلين
    issueDateInput.addEventListener('change', updateExpiryDate);
    validitySelect.addEventListener('change', updateExpiryDate);
});
</script>