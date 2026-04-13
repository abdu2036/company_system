@extends('layouts.admin')

@section('title', 'تعديل اشتراك الغرفة التجارية')

@section('content_header')
<h1 class="text-right">تعديل اشتراك الغرفة التجارية 🏢</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-warning text-right">
        <h3 class="card-title float-right text-dark font-weight-bold">تعديل بيانات السجل رقم:
            {{ $chamber->chamber_number }}</h3>
    </div>

    <div class="card-body">
        <form action="{{ route('chambers.update', $chamber->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row text-right" style="direction: rtl;">

                {{-- اختيار الشركة --}}
                <div class="form-group col-md-12">
                    <label>الشركة المرتبطة <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-control" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ $chamber->company_id == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- رقم العضوية --}}
                <div class="form-group col-md-4">
                    <label>رقم العضوية <span class="text-danger">*</span></label>
                    <input type="text" name="chamber_number" class="form-control" value="{{ $chamber->chamber_number }}"
                        required>
                </div>

                {{-- تاريخ الاشتراك --}}
                <div class="form-group col-md-4">
                    <label>تاريخ الاشتراك <span class="text-danger">*</span></label>
                    <input type="date" name="issue_date" id="issue_date" class="form-control"
                        value="{{ $chamber->issue_date }}" required>
                </div>

                {{-- تاريخ الانتهاء --}}
                <div class="form-group col-md-4">
                    <label>تاريخ الانتهاء <span class="text-danger">*</span></label>
                    <input type="date" name="expiry_date" id="expiry_date" class="form-control"
                        value="{{ $chamber->expiry_date }}" required>
                </div>

                {{-- تحديث المرفق --}}
                <div class="form-group col-md-12">
                    <label>تحديث الشهادة (اختياري)</label>
                    @if($chamber->document_path)
                        <div class="mb-2">
                            <a href="{{ asset($chamber->document_path) }}" target="_blank"
                                class="text-success border p-1 d-inline-block rounded">
                                <i class="fas fa-file-pdf"></i> عرض الملف الحالي
                            </a>
                        </div>
                    @endif
                    <input type="file" class="form-control" onchange="uploadFile(this, 'chamber')">

                    <div id="upload_status" class="mt-2"></div>
                    <input type="hidden" name="temp_file_path" id="temp_file_path">
                </div>
            </div>

            <div class="card-footer text-left mt-3">
                <button type="submit" class="btn btn-warning shadow-sm font-weight-bold">حفظ التعديلات النهائية</button>
                <a href="{{ route('chambers.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // حساب تاريخ الانتهاء تلقائياً عند تغيير تاريخ الاشتراك
        $('#issue_date').on('change', function () {
            let subDate = new Date($(this).val());
            if (!isNaN(subDate.getTime())) {
                subDate.setFullYear(subDate.getFullYear() + 1);
                $('#expiry_date').val(subDate.toISOString().split('T')[0]);
            }
        });

        // كود الرفع AJAX (نفس الكود المستخدم في الإضافة)
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
                        statusDiv.innerHTML = '<span class="text-success font-weight-bold">✅ تم تجهيز الملف الجديد للتحميل</span>';
                        document.getElementById('temp_file_path').value = data.temp_path;
                    }
                });
        };
    });
</script>
@stop