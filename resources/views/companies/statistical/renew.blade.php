@extends('layouts.admin')
@section('title', 'تجديد الصلاحية الرموز الإحصائية للشركات')
@section('content')
<div class="container-fluid pt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-path"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-path"><a href="{{ route('statistical.index') }}"> / سجل الرموز الإحصائية</a></li>
            <li class="breadcrumb-item active" aria-current="page"> / تجديد الصلاحية</li>
        </ol>
    </nav>

    <div class="card card-purple text-right" style="direction: rtl; border-top: 3px solid #6f42c1;">
        <div class="card-header text-white" style="background-color: #6f42c1;">
            <h3 class="card-title float-right mb-0">
                <i class="fas fa-sync-alt"></i> تجديد صلاحية الرمز الإحصائي للشركة: {{ optional($register->company)->name }}
            </h3>
        </div>

        <form action="{{ route('statistical.processRenew', $register->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card-body">
                <div class="row bg-light p-3 rounded mb-4" style="border-right: 4px solid #6f42c1;">
                    <div class="col-md-6">
                        <strong>رقم الرمز الإحصائي الحالي:</strong> 
                        <span class="badge badge-secondary p-2 mr-2">{{ $register->statistical_code }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>تاريخ الانتهاء السابق المُراد تجديده:</strong> 
                        <span class="text-danger font-weight-bold mr-2">{{ $register->expiry_date->format('Y-m-d') }}</span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-4 form-group">
                        <label for="issue_date">تاريخ الإصدار الجديد <span class="text-danger">*</span> <i class="fas fa-calendar-alt text-purple" style="color:#6f42c1;"></i></label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control @error('issue_date') is-invalid @enderror" value="{{ old('issue_date', date('Y-m-d')) }}">
                        @error('issue_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="duration">مدة الصلاحية الجديدة <i class="fas fa-hourglass-half text-warning"></i></label>
                        <select name="duration" id="duration" class="form-control">
                            <option value="سنة واحدة" {{ old('duration') == 'سنة واحدة' ? 'selected' : '' }}>سنة واحدة</option>
                            <option value="سنتين" {{ old('duration') == 'سنتين' ? 'selected' : '' }}>سنتين</option>
                            <option value="3 سنوات" {{ old('duration') == '3 سنوات' ? 'selected' : '' }}>3 سنوات</option>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="expiry_date">تاريخ الانتهاء الجديد المحسوب <i class="fas fa-calendar-check text-success"></i></label>
                        <input type="date" id="expiry_date" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12 form-group">
                        <label for="attachment">إرفاق ملف أو نسخة الرمز الإحصائي الجديد (PDF/Image) <i class="fas fa-file-upload text-purple" style="color:#6f42c1;"></i></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="attachment" id="attachment" class="custom-file-input">
                                <label class="custom-file-label text-left" for="attachment" id="file-label">قم باختيار مستند التجديد الجديد إن وجد</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-left bg-light">
                <button type="submit" class="btn text-white px-4" style="background-color: #6f42c1;">تأكيد وحفظ التجديد</button>
                <a href="{{ route('statistical.index') }}" class="btn btn-secondary px-4 mr-2">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const issueDateInput = document.getElementById('issue_date');
    const durationSelect = document.getElementById('duration');
    const expiryDateInput = document.getElementById('expiry_date');
    const fileInput = document.getElementById('attachment');
    const fileLabel = document.getElementById('file-label');

    // دالة احتساب تاريخ الانتهاء الفوري للتجديد
    function calculateExpiry() {
        const issueDateValue = issueDateInput.value;
        if (!issueDateValue) {
            expiryDateInput.value = '';
            return;
        }

        let date = new Date(issueDateValue);
        const duration = durationSelect.value;

        if (duration === 'سنة واحدة') date.setFullYear(date.getFullYear() + 1);
        else if (duration === 'سنتين') date.setFullYear(date.getFullYear() + 2);
        else if (duration === '3 سنوات') date.setFullYear(date.getFullYear() + 3);

        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        
        expiryDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    // تفعيل دالة الحساب مباشرة عند التحميل وعند التغيير
    calculateExpiry();
    issueDateInput.addEventListener('change', calculateExpiry);
    durationSelect.addEventListener('change', calculateExpiry);

    // تبديل النص لاسم الملف المرفق الجديد
    fileInput.addEventListener('change', function (e) {
        if (e.target.files.length > 0) {
            fileLabel.textContent = e.target.files[0].name;
        }
    });
});
</script>
@endsection