@extends('layouts.admin') {{-- تأكد من مطابقة اسم الليأوت الأساسي لديك --}}

@section('content')
<div class="container-fluid pt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-path"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page"> / إضافة سجل مستورد</li>
        </ol>
    </nav>

    <div class="card card-info text-right" style="direction: rtl;">
        <div class="card-header" style="background-color: #17a2b8; color: #fff;">
            <h3 class="card-title float-right mb-0">
                <i class="fas fa-file-invoice"></i> بيانات الرمز الإحصائي 📝
            </h3>
        </div>

        <form action="{{ route('statistical.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="company_id">اختيار الشركة <span class="text-danger">*</span></label>
                        <select name="company_id" id="company_id" class="form-control @error('company_id') is-invalid @enderror">
                            <option value="">-- اختر الشركة --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-4 form-group">
                        <label for="statistical_code">رقم الرمز الإحصائي <span class="text-danger">*</span> <i class="fas fa-id-card text-primary"></i></label>
                        <input type="text" name="statistical_code" id="statistical_code" class="form-control @error('statistical_code') is-invalid @enderror" value="{{ old('statistical_code') }}" placeholder="أدخل رقم الرمز الإحصائي">
                        @error('statistical_code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="issue_date">تاريخ الإصدار <span class="text-danger">*</span> <i class="fas fa-calendar-alt text-primary"></i></label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control @error('issue_date') is-invalid @enderror" value="{{ old('issue_date') }}">
                        @error('issue_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="duration">مدة الصلاحية <i class="fas fa-hourglass-half text-warning"></i></label>
                        <select name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror">
                            <option value="سنة واحدة" {{ old('duration') == 'سنة واحدة' ? 'selected' : '' }}>سنة واحدة</option>
                            <option value="سنتين" {{ old('duration') == 'سنتين' ? 'selected' : '' }}>سنتين</option>
                            <option value="3 سنوات" {{ old('duration') == '3 سنوات' ? 'selected' : '' }}>3 سنوات</option>
                        </select>
                        @error('duration')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6 form-group">
                        <label for="expiry_date">تاريخ الانتهاء <i class="fas fa-calendar-check text-danger"></i></label>
                        <input type="date" id="expiry_date" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="attachment">إرفاق ملف الرمز الإحصائي (PDF/Image) <i class="fas fa-folder-open text-warning"></i></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="attachment" id="attachment" class="custom-file-input @error('attachment') is-invalid @enderror">
                                <label class="custom-file-label text-left" for="attachment" id="file-label">لم يتم اختيار أي ملف</label>
                            </div>
                        </div>
                        @error('attachment')
                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="card-footer text-left bg-light">
                <button type="submit" class="btn btn-info text-white px-4">حفظ بيانات السجل</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary px-4 mr-2">إلغاء</a>
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

    // دالة حساب تاريخ الانتهاء تلقائياً
    function calculateExpiry() {
        const issueDateValue = issueDateInput.value;
        if (!issueDateValue) {
            expiryDateInput.value = '';
            return;
        }

        let date = new Date(issueDateValue);
        const duration = durationSelect.value;

        if (duration === 'سنة واحدة') {
            date.setFullYear(date.getFullYear() + 1);
        } else if (duration === 'سنتين') {
            date.setFullYear(date.getFullYear() + 2);
        } else if (duration === '3 سنوات') {
            date.setFullYear(date.getFullYear() + 3);
        }

        // تنسيق التاريخ بصيغة YYYY-MM-DD ليظهر في حقل الإدخال بشكل صحيح
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        
        expiryDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    // تشغيل الحساب عند تغيير تاريخ الإصدار أو مدة الصلاحية
    issueDateInput.addEventListener('change', calculateExpiry);
    durationSelect.addEventListener('change', calculateExpiry);

    // تحديث اسم الملف المرفق عند الاختيار
    fileInput.addEventListener('change', function (e) {
        if (e.target.files.length > 0) {
            fileLabel.textContent = e.target.files[0].name;
        } else {
            fileLabel.textContent = 'لم يتم اختيار أي ملف';
        }
    });
});
</script>
@endsection