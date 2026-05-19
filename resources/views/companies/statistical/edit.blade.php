@extends('layouts.admin')
@section('title', 'تعديل الرمز الإحصائي للشركة')
@section('content')
<div class="container-fluid pt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-path"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-path"><a href="{{ route('statistical.index') }}"> / سجل الرموز الإحصائية</a></li>
            <li class="breadcrumb-item active" aria-current="page"> / تعديل السجل</li>
        </ol>
    </nav>

    <div class="card card-warning text-right" style="direction: rtl;">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title float-right mb-0">
                <i class="fas fa-edit"></i> تعديل بيانات الرمز الإحصائي #{{ $register->id }}
            </h3>
        </div>

        <form action="{{ route('statistical.update', $register->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- مهم جداً لإخبار لارافل بنوع عملية التحديث --}}
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="company_id">اختيار الشركة <span class="text-danger">*</span></label>
                        <select name="company_id" id="company_id" class="form-control @error('company_id') is-invalid @enderror">
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ (old('company_id', $register->company_id) == $company->id) ? 'selected' : '' }}>
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
                        <label for="statistical_code">رقم الرمز الإحصائي <span class="text-danger">*</span></label>
                        <input type="text" name="statistical_code" id="statistical_code" class="form-control @error('statistical_code') is-invalid @enderror" value="{{ old('statistical_code', $register->statistical_code) }}">
                        @error('statistical_code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="issue_date">تاريخ الإصدار <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control @error('issue_date') is-invalid @enderror" value="{{ old('issue_date', $register->issue_date->format('Y-m-d')) }}">
                        @error('issue_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="duration">مدة الصلاحية</label>
                        <select name="duration" id="duration" class="form-control">
                            <option value="سنة واحدة" {{ old('duration', $register->duration) == 'سنة واحدة' ? 'selected' : '' }}>سنة واحدة</option>
                            <option value="سنتين" {{ old('duration', $register->duration) == 'سنتين' ? 'selected' : '' }}>سنتين</option>
                            <option value="3 سنوات" {{ old('duration', $register->duration) == '3 سنوات' ? 'selected' : '' }}>3 سنوات</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6 form-group">
                        <label for="expiry_date">تاريخ الانتهاء المحسوب</label>
                        <input type="date" id="expiry_date" class="form-control" readonly style="background-color: #e9ecef;" value="{{ $register->expiry_date->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="attachment">استبدال ملف الرمز الإحصائي (اتركه فارغاً للاحتفاظ بالقديم)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="attachment" id="attachment" class="custom-file-input">
                                <label class="custom-file-label text-left" for="attachment" id="file-label">
                                    {{ $register->attachment ? 'يوجد ملف مرفق بالفعل' : 'لم يتم اختيار أي ملف' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-left bg-light">
                <button type="submit" class="btn btn-warning px-4">تحديث البيانات</button>
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

    issueDateInput.addEventListener('change', calculateExpiry);
    durationSelect.addEventListener('change', calculateExpiry);

    fileInput.addEventListener('change', function (e) {
        if (e.target.files.length > 0) {
            fileLabel.textContent = e.target.files[0].name;
        }
    });
});
</script>
@endsection