@extends('layouts.admin')
@section('title', 'تجديد السجل التجاري')

@section('content_header')
    <h1 class="text-right">تجديد السجل التجاري 🔄</h1>
@stop

@section('content')
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-ban"></i> تنبيه!</h5>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="card card-primary">
    <div class="card-body">
        <form action="{{ route('commercial-registers.updateRenew', $register->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- معلومات الشركة (للقراءة فقط) --}}
            <div class="row">
                <div class="form-group col-md-6">
                    <label>اسم الشركة 🏢</label>
                    <input type="text" class="form-control" value="{{ $register->company->name }}" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label>رقم السجل التجاري 🔢</label>
                    <input type="text" class="form-control" value="{{ $register->cr_number }}" readonly>
                </div>
            </div>

            <hr>

            {{-- حقول التجديد --}}
            <div class="row">
                <div class="form-group col-md-4">
                    <label>تاريخ التجديد السجل 📅 <span class="text-danger">*</span></label>
                    <input type="date" id="renewal_date" name="renewal_date" class="form-control" required>
                </div>
                
                <div class="form-group col-md-4">
                    <label>تاريخ الانتهاء الجديد (تلقائي) ⌛ <span class="text-danger">*</span></label>
                    <input type="date" name="cr_expiry_date" id="cr_expiry_date" class="form-control" required>
                </div>

                <div class="form-group col-md-4">
                    <label>إرفاق السجل الجديد 📄 </label>
                    <input type="file" id="cr_upload" class="form-control" onchange="uploadFile(this, 'commercial_register')">
                    <input type="hidden" name="temp_file_path" id="temp_file_path" >
                    <small class="text-muted" id="upload_status"></small>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success btn-lg">حفظ بيانات التجديد ✅</button>
<a href="{{ route('commercial-registers.index') }}" class="btn btn-secondary btn-lg">إلغاء</a>            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // حساب تاريخ الانتهاء تلقائياً عند تغيير تاريخ التجديد
    $('#renewal_date').on('change', function() {
        let renewalDate = new Date($(this).val());
        
        if (!isNaN(renewalDate.getTime())) {
            // إضافة سنة واحدة (365 يوم)
            renewalDate.setFullYear(renewalDate.getFullYear() + 1);
            
            // تحويل التاريخ لصيغة YYYY-MM-DD
            let expiryDate = renewalDate.toISOString().split('T')[0];
            
            // وضع القيمة في حقل تاريخ الانتهاء
            $('#cr_expiry_date').val(expiryDate);
        }
    });
});

// دالة رفع الملفات المؤقتة عبر AJAX
function uploadFile(input, folder) {
    let file = input.files[0];
    let formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');

    $('#upload_status').text('جاري الرفع... ⏳');

    $.ajax({
        url: '/upload-temp', // تأكد من مطابقة هذا المسار لما في الـ Routes
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.status === 'success') {
                $('#temp_file_path').val(response.temp_path);
                $('#upload_status').text('تم الرفع بنجاح ✅').addClass('text-success');
            }
        },
        error: function() {
            $('#upload_status').text('فشل الرفع ❌').addClass('text-danger');
        }
    });
}
</script>
@stop