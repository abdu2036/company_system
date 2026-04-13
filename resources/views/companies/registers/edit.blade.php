@extends('layouts.admin')

@section('title', 'تعديل سجل: ' . $register->cr_number)

@section('content_header')
    <h1 class="text-right">إدارة السجلات التجارية 📃</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-info text-right">
        <h3 class="card-title float-right">تعديل سجل: {{ $register->cr_number }}</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('commercial-registers.update', $register->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row text-right" style="direction: rtl;">
                {{-- رقم السجل التجاري --}}
                <div class="form-group col-md-4">
                    <label>رقم السجل التجاري <span class="text-danger">*</span></label>
                    <input type="text" name="cr_number" id="cr_number" class="form-control text-right" 
                           value="{{ old('cr_number', $register->cr_number) }}" required>
                </div>

                {{-- رقم الهاتف --}}
                <div class="form-group col-md-4">
                    <label>رقم الهاتف <span class="text-danger">*</span></label>
                    <input type="text" name="phone" id="phone" class="form-control text-right" 
                           value="{{ old('phone', $register->phone) }}" required>
                </div>

                {{-- اسم المفوض --}}
                <div class="form-group col-md-4">
                    <label>اسم المفوض <span class="text-danger">*</span></label>
                    <input type="text" name="representative_name" id="representative_name" class="form-control text-right" 
                           value="{{ old('representative_name', $register->representative_name) }}" required>
                </div>

                {{-- تاريخ إصدار السجل --}}
                <div class="form-group col-md-6">
                    <label>تاريخ إصدار السجل <span class="text-danger">*</span></label>
                    <input type="date" name="cr_issue_date" id="cr_issue_date" class="form-control" 
                           value="{{ old('cr_issue_date', $register->cr_issue_date) }}" required>
                </div>

                {{-- تاريخ انتهاء السجل (يُحسب تلقائياً) --}}
                <div class="form-group col-md-6">
                    <label>تاريخ انتهاء السجل <span class="text-danger">*</span></label>
                    <input type="date" name="cr_expiry_date" id="cr_expiry_date" class="form-control" 
                           value="{{ old('cr_expiry_date', $register->expiry_date) }}" required>
                </div>

                {{-- تحديث المرفق --}}
                <div class="form-group col-md-12">
                    <label>تحديث المرفق (PDF/Image)</label>
                    <input type="file" id="cr_upload" class="form-control" onchange="uploadFile(this, 'commercial_register')">
                    
                    <div id="upload_status" class="mt-2">
                        @if($register->document_path)
                             <span class="text-muted small">يوجد ملف مرفق مسبقاً ✅</span>
                        @endif
                    </div>
                    <input type="hidden" name="temp_file_path" id="temp_file_path">
                </div>
            </div>

            <div class="card-footer text-left mt-3">
                <button type="submit" class="btn btn-info shadow-sm">حفظ التعديلات النهائية</button>
                <a href="{{ route('commercial-registers.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // 1. الحساب التلقائي لتاريخ الانتهاء (سنة واحدة من تاريخ الإصدار)
    $('#cr_issue_date').on('change', function() {
        let issueDate = new Date($(this).val());
        if (!isNaN(issueDate.getTime())) {
            issueDate.setFullYear(issueDate.getFullYear() + 1);
            document.getElementById('cr_expiry_date').value = issueDate.toISOString().split('T')[0];
        }
    });

    // 2. وظيفة رفع الملفات عبر AJAX
window.uploadFile = function(input, type) {
    let file = input.files[0];
    if (!file) return;

    let formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);

    let statusDiv = document.getElementById('upload_status');
    let hiddenInput = document.getElementById('temp_file_path');

    statusDiv.innerHTML = '<span class="text-info font-weight-bold">جاري الرفع... ⏳</span>';

    fetch('/upload-temp', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // التعديل هنا: نتحقق من status ونستخدم temp_path كما في الكنترولر الخاص بك
        if (data.status === 'success') {
            statusDiv.innerHTML = '<span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> تم رفع الملف بنجاح وجاهز للحفظ ✅</span>';
            hiddenInput.value = data.temp_path; // نستخدم temp_path بدلاً من path
        } else {
            statusDiv.innerHTML = '<span class="text-danger font-weight-bold">❌ ' + (data.message || 'فشل الرفع') + '</span>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        statusDiv.innerHTML = '<span class="text-danger font-weight-bold">❌ خطأ في الاتصال بالسيرفر</span>';
    });
};
});
</script>
@stop