@extends('layouts.admin') {{-- تأكد من اسم الليوت عندك --}}
@section('title', 'السجل المالي للشركة') {{-- عنوان الصفحة --}}
@section('content')
<div class="container-fluid" style="direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
    <div class="card card-success card-outline shadow-lg">
        <div class="card-header">
            <h3 class="card-title float-right">
                <i class="fas fa-file-invoice-dollar ml-2"></i>
                السجل المالي لشركة: <span class="text-primary">{{ $company->name }}</span>
            </h3>
        </div>

        <form action="{{ route('finance.store') }}" method="POST">
            @csrf
            <input type="hidden" name="company_id" value="{{ $company->id }}">

            <div class="card-body">
                <table class="table table-bordered table-striped text-center">
                    <thead class="bg-success text-white">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 25%">الخدمة</th>
                            <th style="width: 15%">الإجراء</th>
                            <th style="width: 10%">العدد</th>
                            <th style="width: 15%">المبلغ</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody id="finance-table-body">
                        @foreach($services as $index => $service)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <input type="hidden" name="services[{{ $index }}][name]" value="{{ $service->name }}">
                                <strong>{{ $service->name }}</strong>
                            </td>
                            <td>
                                <select name="services[{{ $index }}][action]" class="form-control">
                                    <option value="تأسيس">تأسيس</option>
                                    <option value="تجديد">تجديد</option>
                                    <option value="تعديل">تعديل</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="services[{{ $index }}][quantity]" 
                                       class="form-control qty-input" value="1" min="1">
                            </td>
                            <td>
                                <input type="number" name="services[{{ $index }}][price]" 
                                       class="form-control price-input" placeholder="0.00">
                            </td>
                            <td>
                                <input type="text" name="services[{{ $index }}][notes]" class="form-control" placeholder="ملاحظات...">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <hr>

                <div class="row mt-4 justify-content-end">
                    <div class="col-md-4">
                        <div class="info-box bg-light border">
                            <div class="info-box-content text-dark">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>المجموع الكلي:</strong>
                                    <input type="number" name="total_amount" id="grand-total" class="form-control w-50" readonly value="0">
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>الواصل:</strong>
                                    <input type="number" name="paid_amount" id="paid-amount" class="form-control w-50" value="0">
                                </div>
                                <div class="d-flex justify-content-between text-danger">
                                    <strong>الباقي:</strong>
                                    <input type="number" name="remaining_amount" id="remaining-amount" class="form-control w-50" readonly value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-left">
                <button type="submit" class="btn btn-success px-5">
                    <i class="fas fa-save ml-1"></i> حفظ السجل المالي
                </button>
                <a href="{{ route('finance.index') }}" class="btn btn-secondary px-5">إلغاء</a>
            </div>
        </form>
    </div>
</div>

{{-- كود الحسابات التلقائية --}}
<script>
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('price-input') || e.target.classList.contains('qty-input') || e.target.id === 'paid-amount') {
        calculateTotals();
    }
});

function calculateTotals() {
    let grandTotal = 0;
    const prices = document.querySelectorAll('.price-input');
    const quantities = document.querySelectorAll('.qty-input');

    prices.forEach((priceInput, index) => {
        let val = parseFloat(priceInput.value) || 0;
        let qty = parseFloat(quantities[index].value) || 1;
        grandTotal += (val * qty);
    });

    const paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    
    document.getElementById('grand-total').value = grandTotal.toFixed(2);
    document.getElementById('remaining-amount').value = (grandTotal - paid).toFixed(2);
}
</script>
@endsection