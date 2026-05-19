@extends('layouts.admin')
@section('title', 'إضافة سجل صناعي جديد')
@section('content')
<div class="container-fluid pt-4" style="direction: rtl;">
    <nav aria-label="breadcrumb">
       
    </nav>

    <div class="row justify-content-center mt-3">
        <div class="col-md-12">
            <div class="card shadow-sm text-left border-0">
                
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #17a2b8; display: flex !important; flex-direction: row-reverse;">
                    <h5 class="card-title mb-0 font-weight-bold" style="font-size: 1.1rem;">
                         بيانات الرمز الصناعي 📝 <i class="fas fa-file-alt mr-1"></i>
                    </h5>
                </div>

                <form action="{{ route('industrial.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body bg-white px-4 py-4">
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="company_id" class="font-weight-bold mb-2">اختيار الشركة <span class="text-danger">*</span></label>
                                <select name="company_id" id="company_id" class="form-control select2 text-left @error('company_id') is-invalid @enderror" style="width: 100%; height: calc(2.25rem + 2px);">
                                    <option value="">-- اختر الشركة --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
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
                                <input type="text" name="industrial_code" id="industrial_code" class="form-control text-left @error('industrial_code') is-invalid @enderror" placeholder="أدخل رقم الرمز الصناعي" value="{{ old('industrial_code') }}">
                                @error('industrial_code')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="issue_date" class="font-weight-bold mb-2">
                                    تاريخ الإصدار <span class="text-danger">*</span> <i class="fas fa-calendar-alt text-primary mx-1"></i>
                                </label>
                                <input type="date" name="issue_date" id="issue_date" class="form-control text-left @error('issue_date') is-invalid @enderror" value="{{ old('issue_date') }}">
                                @error('issue_date')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="duration" class="font-weight-bold mb-2">
                                    مدة الصلاحية <span class="text-danger">*</span> <i class="fas fa-hourglass-half text-warning mx-1"></i>
                                </label>
                                <select name="duration" id="duration" class="form-control text-left @error('duration') is-invalid @enderror">
                                    <option value="سنة واحدة" {{ old('duration') == 'سنة واحدة' ? 'selected' : '' }}>سنة واحدة</option>
                                    <option value="سنتين" {{ old('duration') == 'سنتين' ? 'selected' : '' }}>سنتين</option>
                                    <option value="3 سنوات" {{ old('duration') == '3 سنوات' ? 'selected' : '' }}>3 سنوات</option>
                                </select>
                                @error('duration')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="font-weight-bold mb-2">
                                    تاريخ الانتهاء <i class="fas fa-calendar-times text-danger mx-1"></i>
                                </label>
                                <input type="date" id="expiry_date" class="form-control text-left bg-light" readonly placeholder="ة ن س / ر هـ ش / م و ي">
                            </div>

                            <div class="col-md-6">
                                <label for="attachment" class="font-weight-bold mb-2">
                                    إرفاق ملف الرمز الصناعي (PDF/Image) <i class="fas fa-folder text-warning mx-1"></i>
                                </label>
                                <div class="custom-file" style="direction: ltr;">
                                    <input type="file" name="attachment" id="attachment" class="custom-file-input @error('attachment') is-invalid @enderror">
                                    <label class="custom-file-label text-left w-100" for="attachment" data-browse="Browse" style="left: 0; left: 0; text-align: left; padding-left: 15px;">اختر الملف...</label>
                                </div>
                                @error('attachment')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-muted"><i class="fas fa-info-circle"></i> سيقوم النظام باحتساب تاريخ الانتهاء تلقائياً بمجرد إدخال تاريخ الإصدار وتحديد المدة.</small>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-white border-0 d-flex justify-content-start" style="display: flex !important; gap: 10px; flex-direction: row-reverse; padding-bottom: 25px;">
                        <button type="submit" class="btn text-white px-4 font-weight-bold shadow-sm" style="background-color: #17a2b8;">
                            حفظ بيانات السجل
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
    // 1. تحديث اسم الملف المرفق
    const fileInput = document.getElementById('attachment');
    if(fileInput) {
        fileInput.addEventListener('change', function(e){
            const fileName = e.target.files[0] ? e.target.files[0].name : 'اختر الملف...';
            const label = e.target.nextElementSibling;
            label.innerText = fileName;
        });
    }

    // 2. احتساب تاريخ الانتهاء تلقائياً
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

    // التنفيذ الفوري عند التغيير
    issueDateInput.addEventListener('change', calculateExpiry);
    durationSelect.addEventListener('change', calculateExpiry);
});
</script>
@endsection