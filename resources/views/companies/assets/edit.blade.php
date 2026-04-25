@extends('layouts.admin')

@section('title', 'تعديل الأصل: ' . $asset->name)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-bold text-dark">
                    <i class="fas fa-edit mr-2 text-primary"></i>تعديل بيانات الأصل
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-right mr-1"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-lg border-0" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-bottom">
                <h3 class="card-title text-muted mt-1">
                    <i class="fas fa-info-circle mr-2 text-info"></i> 
                    تعديل الأصل: <span class="badge badge-dark ml-1">{{ $asset->asset_code }}</span>
                </h3>
            </div>
            
            <form action="{{ route('assets.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body p-4">
                    <div class="row mt-2">
                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold mb-2">اسم الأصل <span class="text-danger">*</span></label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-box text-primary"></i></span>
                                </div>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $asset->name) }}" required>
                            </div>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold mb-2">الشركة التابعة <span class="text-danger">*</span></label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-building text-primary"></i></span>
                                </div>
                                <select name="company_id" class="form-control" required>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ $asset->company_id == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="font-weight-bold mb-2">التصنيف</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tags text-primary"></i></span>
                                </div>
                                <input type="text" name="category" class="form-control" value="{{ old('category', $asset->category) }}">
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="font-weight-bold mb-2">الموقع / القسم</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                </div>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $asset->location) }}">
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="font-weight-bold mb-2">حالة الأصل الحالية</label>
                            <div class="input-group custom-input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-sync text-primary"></i></span>
                                </div>
                                <select name="status" class="form-control font-weight-bold {{ $asset->status == 'تالف' ? 'text-danger' : 'text-success' }}">
                                    <option value="جديد" {{ $asset->status == 'جديد' ? 'selected' : '' }}>جديد</option>
                                    <option value="مستعمل" {{ $asset->status == 'مستعمل' ? 'selected' : '' }}>مستعمل</option>
                                    <option value="تحت الصيانة" {{ $asset->status == 'تحت الصيانة' ? 'selected' : '' }}>تحت الصيانة</option>
                                    <option value="تالف" class="text-danger font-weight-bold" {{ $asset->status == 'تالف' ? 'selected' : '' }}>
                                        ⚠️ تالف (سيتم النقل للمخزن)
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-weight-bold mb-2">ملاحظات فنية / أسباب التلف</label>
                            <textarea name="notes" class="form-control shadow-sm" rows="4" 
                                      placeholder="اكتب هنا حالة الجهاز بالتفصيل، أو سبب النقل للتوالف...">{{ old('notes', $asset->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light p-4 text-right border-top">
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow border-0" style="border-radius: 10px;">
                        <i class="fas fa-check-circle mr-1"></i> حفظ وتحديث البيانات
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn btn-link text-secondary ml-2 font-weight-bold">إلغاء العملية</a>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
    /* معالجة مشاكل التنسيق في القالب */
    .custom-input-group .input-group-text {
        background-color: #f8f9fa !important;
        border: 1px solid #ced4da !important;
        border-radius: 8px 0 0 8px !important;
        min-width: 45px;
        justify-content: center;
    }
    .custom-input-group .form-control {
        border-radius: 0 8px 8px 0 !important;
        border: 1px solid #ced4da !important;
        height: calc(2.25rem + 10px);
    }
    .custom-input-group .form-control:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.10) !important;
    }
    textarea.form-control {
        border-radius: 8px !important;
        border: 1px solid #ced4da !important;
    }
    .card { border-radius: 15px !important; }
    .badge { padding: 8px 12px; font-size: 0.9rem; }
</style>
@endsection