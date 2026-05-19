@extends('layouts.admin')
@section('title', 'تعديل السجل الصناعي')
@section('content')
<div class="container-fluid pt-4" style="direction: rtl;">
    <nav aria-label="breadcrumb">
    
    </nav>

    <div class="row justify-content-center mt-3">
        <div class="col-md-12">
            <div class="card shadow-sm text-left border-0">
                
                <div class="card-header  text-white d-flex justify-content-between align-items-center" style="background-color: #17a2b8; display: flex !important; flex-direction: row-reverse;">
                    <h5 class="card-title mb-0 font-weight-bold" style="font-size: 1.1rem;">
                        تعديل بيانات الرمز الصناعي 📝 <i class="fas fa-edit mr-1"></i>
                    </h5>
                </div>

                <form action="{{ route('industrial.update', $register->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body bg-white px-4 py-4">

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="company_id" class="font-weight-bold mb-2">الشركة التابع لها <span class="text-danger">*</span></label>
                                <select name="company_id" id="company_id" class="form-control select2 text-left @error('company_id') is-invalid @enderror" style="width: 100%; height: calc(2.25rem + 2px);">
                                    <option value="">-- اختر الشركة --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $register->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="industrial_code" class="font-weight-bold mb-2">
                                    رقم الرمز الصناعي <span class="text-danger">*</span> <i class="fas fa-id-card text-primary mx-1"></i>
                                </label>
                                <input type="text" name="industrial_code" id="industrial_code" class="form-control text-left @error('industrial_code') is-invalid @enderror" placeholder="أدخل رقم الرمز الصناعي" value="{{ old('industrial_code', $register->industrial_code) }}">
                                @error('industrial_code')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="issue_date" class="font-weight-bold mb-2">
                                    تاريخ الإصدار <span class="text-danger">*</span> <i class="fas fa-calendar-alt text-primary mx-1"></i>
                                </label>
                                <input type="date" name="issue_date" id="issue_date" class="form-control text-left @error('issue_date') is-invalid @enderror" value="{{ old('issue_date', $register->issue_date ? $register->issue_date->format('Y-m-d') : '') }}">
                                @error('issue_date')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="duration" class="font-weight-bold mb-2">
                                    مدة الصلاحية <span class="text-danger">*</span> <i class="fas fa-hourglass-half text-warning mx-1"></i>
                                </label>
                                <select name="duration" id="duration" class="form-control text-left @error('duration') is-invalid @enderror">
                                    <option value="سنة واحدة" {{ old('duration', $register->duration) == 'سنة واحدة' ? 'selected' : '' }}>سنة واحدة</option>
                                    <option value="سنتين" {{ old('duration', $register->duration) == 'سنتين' ? 'selected' : '' }}>سنتين</option>
                                    <option value="3 سنوات" {{ old('duration', $register->duration) == '3 سنوات' ? 'selected' : '' }}>3 سنوات</option>
                                </select>
                                @error('duration')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold mb-2">
                                    تاريخ الانتهاء المتوقع <i class="fas fa-calendar-times text-danger mx-1"></i>
                                </label>
                                <input type="date" id="expiry_date" class="form-control text-left bg-light" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="attachment" class="font-weight-bold mb-2">
                                    تحديث ملف الرمز الصناعي (PDF/Image) <i class="fas fa-folder text-warning mx-1"></i>
                                </label>
                                
                                @if($register->attachment)
                                    <div class="mb-2 d-flex align-items-center justify-content-between bg-light p-2 border rounded" style="font-size: 0.85rem;">
                                        <span class="text-success font-weight-bold"><i class="fas fa-paperclip"></i> يوجد ملف حالي مخزن بالسيرفر</span>
                                        <a href="{{ asset('storage/' . $register->attachment) }}" target="_blank" class="btn btn-xs btn-outline-info py-0 px-2 small">
                                            <i class="fas fa-eye"></i> استعراض المرفق الحالي
                                        </a>
                                    </div>
                                @endif

                                <div class="custom-file" style="direction: ltr;">
                                    <input type="file" name="attachment" id="attachment" class="custom-file-input @error('attachment') is-invalid @enderror">
                                    <label class="custom-file-label text-left w-100" for="attachment" data-browse="Browse" style="left: 0; right: 0; text-align: right; padding-right: 15px;">اتركه فارغاً للاحتفاظ بالقديم...</label>
                                </div>
                                @error('attachment')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-white border-0 d-flex justify-content-start" style="display: flex !important; gap: 10px; flex-direction: row-reverse; padding-bottom: 25px;">
                        <button type="submit" class="btn text-white px-4 font-weight-bold shadow-sm" style="background-color: #17a2b8;">
                            تحديث بيانات السجل
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
    // 1. تحديث حقل ملف الرفع عند الاختيار
    const fileInput = document.getElementById('attachment');
    if(fileInput) {
        fileInput.addEventListener('change', function(e){
            const fileName = e.target.files[0] ? e.target.files[0].name : 'اتركه فارغاً للاحتفاظ بالقديم...';
            const label = e.target.nextElementSibling;
            label.innerText = fileName;
        });
    }

    // 2. دالة احتساب تاريخ الانتهاء تلقائياً
    const issueDateInput = document.getElementById('issue_date');
    const durationSelect = document.getElementById('duration');
    const expiryDateInput = document.getElementById('expiry_date');

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

    // تشغيل الاحتساب التلقائي فور فتح صفحة التعديل ليظهر التاريخ الحالي المخزن
    calculateExpiry();

    // تشغيل عند حدوث أي تعديل في المدخلات
    issueDateInput.addEventListener('change', calculateExpiry);
    durationSelect.addEventListener('change', calculateExpiry);
});
</script>
@endsection