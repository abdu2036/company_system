@extends('layouts.admin')

@section('title', 'إضافة ترخيص جديد')

@section('content_header')
{{-- استخدمنا text-right فقط دون float لضمان الاستقامة --}}
<h1 class="text-right">إضافة ترخيص جديد للشركة 📜</h1>
@stop

@section('content')
<style>
    /* كود الإصلاح الشامل */
    .input-group, .btn, .alert, .card-body {
        direction: rtl !important;
        text-align: right !important;
    }

    /* منع انعكاس الأيقونات */
    .fas, .far, .fa {
        transform: none !important;
    }

    /* توحيد اتجاه الحقول */
    input.form-control, select.form-control, textarea.form-control {
        text-align: right !important;
        direction: rtl !important;
    }
    
    /* ضبط تسميات الحقول لتكون دائماً على اليمين */
    label {
        display: block;
        text-align: right;
        width: 100%;
    }
</style>

<div class="card shadow-sm">
    {{-- تم تغيير float-right إلى text-right في الهيدر --}}
    <div class="card-header bg-info text-right">
        <h3 class="card-title" style="float: right;">بيانات ترخيص الشركة</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('licenses.store') }}" method="POST" id="licenseForm">
            @csrf
            {{-- إضافة justify-content-start تضمن ترتيب العناصر من اليمين --}}
            <div class="row text-right" style="direction: rtl;">

                {{-- اختيار الشركة --}}
                <div class="form-group col-md-12">
                    <label>اختيار الشركة <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-control select2" required>
                        <option value="">-- اختر الشركة --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ (request('company_id') == $company->id) ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- رقم الترخيص --}}
                <div class="form-group col-md-12">
                    <label>رقم الترخيص <span class="text-danger">*</span></label>
                    <input type="text" name="license_number" class="form-control" placeholder="أدخل رقم الترخيص هنا" required>
                </div>

                {{-- قسم التواريخ - ترتيب العناصر من اليمين لليسار --}}
                <div class="col-md-4">
                    <label>تاريخ إصدار الترخيص *</label>
                    <input type="date" name="issue_date" id="issue_date" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>مدة صلاحية الترخيص *</label>
                    <select id="validity_period" name="validity_period" class="form-control" required>
                        <option value="">-- اختر المدة --</option>
                        <option value="1">سنة واحدة</option>
                        <option value="2">سنتين</option>
                        <option value="3">ثلاث سنوات</option>
                        <option value="5">خمس سنوات</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>تاريخ الانتهاء *</label>
                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" required 
                           readonly style="background-color: #e9ecef;">
                </div>

                {{-- رفع الملف --}}
                <div class="form-group col-md-12 mt-4">
                    <label>إرفاق صورة الترخيص (PDF/Image)</label>
                    <input type="file" id="fileInput" class="form-control" onchange="uploadFile(this, 'license')">
                    <div id="upload_status" class="mt-2"></div>
                    <input type="hidden" name="file_path" id="temp_file_path">
                </div>
            </div>

            {{-- تغيير text-left إلى text-right للأزرار لتظهر في المكان الصحيح --}}
            <div class="card-footer text-right mt-3">
                <button type="submit" id="submitBtn" class="btn btn-info shadow-sm font-weight-bold">
                    <i id="submitIcon" class="fas fa-save"></i> 
                    <span id="btnText">حفظ بيانات الترخيص</span>
                </button>
                <a href="{{ route('companies.index') }}" class="btn btn-secondary shadow-sm">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop
@section('js')
<script>
$(document).ready(function() {
    function calculateExpiry() {
        let issueDateVal = $('#issue_date').val();
        let duration = parseInt($('#validity_period').val());

        if (issueDateVal && duration) {
            let date = new Date(issueDateVal);
            date.setFullYear(date.getFullYear() + duration);
            
            let day = ("0" + date.getDate()).slice(-2);
            let month = ("0" + (date.getMonth() + 1)).slice(-2);
            let year = date.getFullYear();
            
            $('#expiry_date').val(year + "-" + month + "-" + day);
        }
    }

    // الربط مع الـ IDs الصحيحة في الكود الخاص بك
    $('#issue_date, #validity_period').on('change', calculateExpiry);
});
</script>
@stop