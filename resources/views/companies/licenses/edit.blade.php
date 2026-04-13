@extends('layouts.admin')

@section('title', 'تعديل بيانات الترخيص')

@section('content_header')
<h1 class="text-right">تعديل بيانات الترخيص ✏️</h1>
@stop

@section('content')
<div class="card shadow-sm border-info">
    {{-- استخدمنا style="display: block !important;" لكسر أي نظام flex قديم يسبب المشكلة --}}
    <div class="card-header bg-info" style="display: block !important; width: 100% !important;">
        {{-- هنا أجبرنا النص على اليمين مع إزالة أي إزاحة (float) قد تكون موروثة --}}
        <h3 class="card-title" style="float: right !important; text-align: right !important; width: auto; color: white !important; font-weight: bold;">
            تعديل بيانات الترخيص لشركة: <span class="text-yellow">{{ $license->company->name }}</span>
        </h3>
    </div>

        <form action="{{ route('licenses.update', $license->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row text-right">
                    {{-- اختيار الشركة --}}
                    <div class="form-group col-md-8">
                        <label class="font-weight-bold">الشركة المرتبطة <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-control select2" required>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $license->company_id == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- رقم الترخيص --}}
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">رقم الترخيص <span class="text-danger">*</span></label>
                        <input type="text" name="license_number" class="form-control" value="{{ $license->license_number }}" required>
                    </div>
                </div>

                <div class="row text-right mt-3">
                    {{-- تاريخ الإصدار --}}
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">تاريخ الإصدار <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" id="issue_date" 
                               class="form-control" 
                               value="{{ $license->issue_date }}" required>
                    </div>

                    {{-- مدة الصلاحية --}}
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">تعديل المدة (بالسنوات)</label>
                        <select id="validity_period_local" class="form-control">
                            <option value="">-- اختر لتحديث التاريخ --</option>
                            <option value="1">سنة واحدة</option>
                            <option value="2">سنتين</option>
                            <option value="3">ثلاث سنوات</option>
                        
                        </select>
                    </div>

                    {{-- تاريخ الانتهاء --}}
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">تاريخ الانتهاء <span class="text-danger">*</span></label>
                        <input type="date" name="expiry_date" id="expiry_date" 
                               class="form-control" 
                               value="{{ $license->expiry_date }}" readonly 
                               style="background-color: #e9ecef;">
                    </div>
                </div>

                {{-- قسم المرفقات --}}
                <div class="row text-right mt-4 border-top pt-3">
                    <div class="form-group col-md-12">
                        <label class="font-weight-bold">تحديث صورة الترخيص (اختياري)</label>
                        @if($license->document_path)
                            <div class="mb-2">
                                <a href="{{ asset($license->document_path) }}" target="_blank" class="text-info">
                                    <i class="fas fa-file-pdf"></i> عرض المرفق الحالي
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control-file" onchange="uploadFileLocal(this, 'license')">
                        <div id="upload_status_local" class="mt-2 font-weight-bold"></div>
                        <input type="hidden" name="temp_file_path" id="temp_file_path">
                    </div>
                </div>
            </div>

            <div class="card-footer text-left mt-3">
                <button type="submit" class="btn btn-info shadow-sm font-weight-bold">حفظ التعديلات النهائية ✅</button>
                <a href="{{ route('licenses.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // حساب تاريخ الانتهاء تلقائياً عند تغيير تاريخ الإصدار أو المدة
        $('#issue_date, #validity_period_local').on('change', function () {
            let issueDateVal = $('#issue_date').val();
            let years = parseInt($('#validity_period_local').val());

            if (issueDateVal && years) {
                let date = new Date(issueDateVal);
                date.setFullYear(date.getFullYear() + years);
                
                let y = date.getFullYear();
                let m = String(date.getMonth() + 1).padStart(2, '0');
                let d = String(date.getDate()).padStart(2, '0');
                
                $('#expiry_date').val(`${y}-${m}-${d}`);
            }
        });

        // دالة الرفع AJAX الخاصة بهذه الصفحة
        window.uploadFileLocal = function (input, type) {
            let file = input.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append('file', file);
            formData.append('type', type);

            let statusDiv = document.getElementById('upload_status_local');
            statusDiv.innerHTML = '<span class="text-info">جاري رفع الملف الجديد... ⏳</span>';

            fetch('/upload-temp', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    statusDiv.innerHTML = '<span class="text-success font-weight-bold">✅ تم تجهيز الملف بنجاح</span>';
                    document.getElementById('temp_file_path').value = data.temp_path;
                } else {
                    statusDiv.innerHTML = '<span class="text-danger">❌ فشل الرفع</span>';
                }
            })
            .catch(error => {
                statusDiv.innerHTML = '<span class="text-danger">❌ خطأ في الاتصال</span>';
            });
        };
    });
</script>
@stop