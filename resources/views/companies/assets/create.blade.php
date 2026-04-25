@extends('layouts.admin')
@section('title', 'إضافة أصل جديد')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-plus-circle"></i> إضافة أصل جديد للمنظومة</h5>
            <a href="{{ route('assets.index') }}" class="btn btn-sm btn-light">العودة للقائمة</a>
        </div>
        <div class="card-body">
            <form action="{{ route('assets.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">الشركة التابعة لها:</label>
                        <select name="company_id" class="form-control" required>
                            <option value="">اختر الشركة...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">اسم الأصل:</label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: لابتوب Dell G15" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">السيريال نمبر (Serial Number):</label>
                        <input type="text" name="serial_number" class="form-control" placeholder="أدخل الرقم التسلسلي للمصنع">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">تصنيف الأصل:</label>
                        <select name="category" class="form-control" required>
                            <option value="إلكترونيات">إلكترونيات</option>
                            <option value="أثاث">أثاث</option>
                            <option value="سيارات">سيارات</option>
                            <option value="معدات">معدات</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">الموقع الحالي:</label>
                        <input type="text" name="location" class="form-control" placeholder="مثال: المكتب الرئيسي / فرع 1" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">تاريخ الشراء:</label>
                        <input type="date" name="purchase_date" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">سعر الشراء (د.ل):</label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control" value="0.00">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label font-weight-bold text-primary">حالة الأصل الحالية:</label>
                        <select name="status" id="asset_status" class="form-control border-primary" onchange="toggleMaintenanceFields()" required>
                            <option value="جديد">جديد</option>
                            <option value="مستعمل">مستعمل</option>
                            <option value="تحت الصيانة">تحت الصيانة (إدخال بيانات العطل)</option>
                            <option value="تالف">تالف (سيتم نقله للمخزن التالف فوراً)</option>
                        </select>
                    </div>

                    <div id="maintenance_section" class="col-12 row" style="display: none; background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0;">
                        <div class="col-md-8 mb-3">
                            <label class="form-label font-weight-bold text-danger">وصف المشكلة / العطل:</label>
                            <textarea name="fault_description" class="form-control" rows="2" placeholder="اشرح المشكلة التقنية هنا..."></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold text-danger">تكلفة الصيانة التقديرية:</label>
                            <input type="number" step="0.01" name="maintenance_cost" class="form-control" value="0.00">
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-save"></i> حفظ الأصل وتوليد الـ QR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // وظيفة إظهار/إخفاء حقول الصيانة بناءً على الحالة المختارة
    function toggleMaintenanceFields() {
        const status = document.getElementById('asset_status').value;
        const section = document.getElementById('maintenance_section');
        
        if (status === 'تحت الصيانة') {
            section.style.display = 'flex';
        } else {
            section.style.display = 'none';
        }
    }
</script>
@endsection