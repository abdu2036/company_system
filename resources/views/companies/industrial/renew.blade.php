@extends('layouts.admin')
@section('title', 'تجديد السجل الصناعي')
@section('content')
<div class="container-fluid pt-4" style="direction: rtl;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 m-0 text-left small" style="direction: rtl;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('industrial.index') }}">سجل السجلات الصناعية</a></li>
            <li class="breadcrumb-item active" aria-current="page"> / تجديد صلاحية السجل</li>
        </ol>
    </nav>

    <div class="row justify-content-center mt-3">
        <div class="col-md-12">
            <div class="card shadow-sm text-left border-0" style="border-top: 4px solid #6f42c1 !important;">
                
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="display: flex !important; flex-direction: row-reverse;">
                    <h5 class="card-title mb-0 font-weight-bold" style="color: #6f42c1; font-size: 1.1rem;">
                         تجديد صلاحية السجل الصناعي: {{ optional($register->company)->name }} <i class="fas fa-sync-alt mr-1"></i>
                    </h5>
                </div>

                <form action="{{ route('industrial.processRenew', $register->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body bg-white px-4 py-4">
                        
                        <div class="row mb-4 bg-light p-3 rounded border-right mx-0" style="border-right: 5px solid #6f42c1 !important;">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <span class="text-muted small d-block">رقم السجل الصناعي الحالي:</span>
                                <span class="h6 font-weight-bold text-dark"><i class="fas fa-barcode text-muted ml-1"></i> {{ $register->industrial_code }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block text-danger">تاريخ الانتهاء السابق (المراد تجديده):</span>
                                <span class="h6 font-weight-bold text-danger"><i class="fas fa-calendar-times ml-1"></i> {{ $register->expiry_date->format('Y-m-d') }}</span>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="issue_date" class="font-weight-bold mb-2">
                                    تاريخ الإصدار الجديد <span class="text-danger">*</span> <i class="fas fa-calendar-alt text-purple mx-1" style="color: #6f42c1;"></i>
                                </label>
                                <input type="date" name="issue_date" id="issue_date" class="form-control text-left @error('issue_date') is-invalid @enderror" value="{{ old('issue_date', date('Y-m-d')) }}">
                                @error('issue_date')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="duration" class="font-weight-bold mb-2">
                                    مدة التجديد <span class="text-danger">*</span> <i class="fas fa-clock text-warning mx-1"></i>
                                </label>
                                <select name="duration" id="duration" class="form-control text-left">
                                    <option value="سنة واحدة" {{ old('duration') == 'سنة واحدة' ? 'selected' : '' }}>سنة واحدة</option>
                                    <option value="سنتين" {{ old('duration') == 'سنتين' ? 'selected' : '' }}>سنتين</option>
                                    <option value="3 سنوات" {{ old('duration') == '3 سنوات' ? 'selected' : '' }}>3 سنوات</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="font-weight-bold mb-2">
                                    تاريخ الانتهاء الجديد (تلقائي) <i class="fas fa-calendar-check text-success mx-1"></i>
                                </label>
                                <input type="date" id="expiry_date" class="form-control text-left bg-light" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="attachment" class="font-weight-bold mb-2">
                                    إرفاق مستند التجديد الجديد (اختياري) <i class="fas fa-folder-open text-warning mx-1"></i>
                                </label>
                                <div class="custom-file" style="direction: ltr;">
                                    <input type="file" name="attachment" id="attachment" class="custom-file-input">
                                    <label class="custom-file-label text-left w-100" for="attachment" data-browse="Browse" style="left: 0; right: 0; text-align: left; padding-left: 15px;">اختر ملف التجديد الجديد (PDF أو صورة)...</label>
                                </div>
                                <small class="form-text text-muted mt-2"><i class="fas fa-info-circle"></i> إذا لم تقم برفع ملف جديد، سيقوم النظام تلقائياً بالاحتفاظ بنسخة المرفق القديم دون حذفها.</small>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-white border-0 d-flex justify-content-start" style="display: flex !important; gap: 10px; flex-direction: row-reverse; padding-bottom: 25px;">
                        <button type="submit" class="btn text-white px-4 font-weight-bold shadow-sm" style="background-color: #6f42c1;">
                            تأكيد تجديد الصلاحية
                        </button>
                        <a href="{{ route('industrial.index') }}" class="btn btn-secondary px-4 shadow-sm">
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const issueDateInput = document.getElementById('issue_date');
    const durationSelect = document.getElementById('duration');
    const expiryDateInput = document.getElementById('expiry_date');

    // دالة تحديث الحساب التلقائي للتاريخ
    function calculateExpiry() {
        if (!issueDateInput.value) return;
        let date = new Date(issueDateInput.value);
        let duration = durationSelect.value;
        
        if (duration === 'سنة واحدة') date.setFullYear(date.getFullYear() + 1);
        else if (duration === 'سنتين') date.setFullYear(date.getFullYear() + 2);
        else if (duration === '3 سنوات') date.setFullYear(date.getFullYear() + 3);
        
        let y = date.getFullYear();
        let m = String(date.getMonth() + 1).padStart(2, '0');
        let d = String(date.getDate()).padStart(2, '0');
        expiryDateInput.value = `${y}-${m}-${d}`;
    }

    // التنفيذ الفوري والربط مع أحداث التغيير
    calculateExpiry();
    issueDateInput.addEventListener('change', calculateExpiry);
    durationSelect.addEventListener('change', calculateExpiry);

    // تحديث نص الليبل عند اختيار ملف المرفقات
    document.getElementById('attachment').addEventListener('change', function(e){
        const fileName = e.target.files[0] ? e.target.files[0].name : 'اختر ملف التجديد الجديد...';
        e.target.nextElementSibling.innerText = fileName;
    });
});
</script>
@endsection