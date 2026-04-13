@extends('layouts.admin')

@section('title', 'تعديل سجل المستوردين')

@section('content_header')
<h1 class="text-right">تعديل سجل المستوردين 🚢</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-warning text-right">
        <h3 class="card-title float-right text-dark font-weight-bold">
            تعديل بيانات سجل المستوردين لشركة:
            <span class="text-primary" style="text-decoration: underline;">
                {{ $importer->company->name ?? 'غير محدد' }}
            </span> 🚢
        </h3>
    </div>

    <div class="card-body">
        <form action="{{ route('importers.update', $importer->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row text-right" style="direction: rtl;">
                {{-- اختيار الشركة - قراءة فقط --}}
                <div class="form-group col-md-12">
                    <label>الشركة المرتبطة <span class="text-danger">*</span></label>
                    <select class="form-control" disabled>
                        <option value="{{ $importer->company_id }}" selected>
                            {{ $importer->company->name ?? 'غير محدد' }}
                        </option>
                    </select>
                    {{-- حقل مخفي لضمان وصول id الشركة للمتحكم عند الحفظ --}}
                    <input type="hidden" name="company_id" value="{{ $importer->company_id }}">
                </div>

                {{-- رقم السجل --}}
                <div class="form-group col-md-4">
                    <label>رقم السجل 🔢 <span class="text-danger">*</span></label>
                    <input type="text" name="importer_number" class="form-control"
                        value="{{ $importer->importer_number }}" required>
                </div>

                {{-- تاريخ الإصدار --}}
                <div class="form-group col-md-4">
                    <label>تاريخ الإصدار 📅 <span class="text-danger">*</span></label>
                    <input type="date" name="issue_date" id="issue_date" class="form-control"
                        value="{{ $importer->issue_date }}" required>
                </div>

                {{-- تاريخ الانتهاء --}}
                <div class="form-group col-md-4">
                    <label>تاريخ الانتهاء ⏳ <span class="text-danger">*</span></label>
                    <input type="date" name="expiry_date" id="expiry_date" class="form-control"
                        value="{{ $importer->expiry_date }}" required>
                </div>

                {{-- تحديث المرفق --}}
                <div class="form-group col-md-12">
                    <label>تحديث الشهادة (اختياري)</label>
                    @if($importer->document_path)
                        <div class="mb-2">
                            <a href="{{ asset($importer->document_path) }}" target="_blank"
                                class="text-success border p-1 d-inline-block rounded">
                                <i class="fas fa-file-pdf"></i> عرض السجل الحالي
                            </a>
                        </div>
                    @endif
                    <input type="file" class="form-control" onchange="uploadFile(this, 'importer')">

                    <div id="upload_status" class="mt-2"></div>
                    <input type="hidden" name="temp_file_path" id="temp_file_path">
                </div>
            </div>

            <div class="card-footer text-left mt-3">
                <button type="submit" class="btn btn-warning shadow-sm font-weight-bold">حفظ التعديلات النهائية</button>
                <a href="{{ route('importers.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // حساب تاريخ الانتهاء تلقائياً عند تغيير تاريخ الإصدار
        $('#issue_date').on('change', function () {
            let subDate = new Date($(this).val());
            if (!isNaN(subDate.getTime())) {
                subDate.setFullYear(subDate.getFullYear() + 1); // إضافة سنة واحدة
                $('#expiry_date').val(subDate.toISOString().split('T')[0]);
            }
        });

        // كود الرفع AJAX
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
                        statusDiv.innerHTML = '<span class="text-success font-weight-bold">✅ تم تجهيز الملف الجديد</span>';
                        document.getElementById('temp_file_path').value = data.temp_path;
                    } else {
                        statusDiv.innerHTML = '<span class="text-danger">❌ فشل الرفع</span>';
                    }
                });
        };
    });
</script>
@stop