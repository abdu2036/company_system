@extends('layouts.admin')
@section('title', 'تاريخ العمليات المالية')
@section('content')
<div class="container-fluid" style="direction: rtl; text-align: right; font-family: 'Cairo', sans-serif;">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-dark d-flex justify-content-between align-items-center">
            <h3 class="card-title text-white mb-0">
                <i class="fas fa-history ml-2"></i>
                تاريخ العمليات المالية لشركة: <span class="text-warning">{{ $company->name }}</span>
            </h3>
            <a href="{{ route('finance.index') }}" class="btn btn-outline-light btn-sm">العودة للقائمة</a>
        </div>
        
        <div class="card-body bg-light">
            @if($invoices->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">لا توجد سجلات مالية محفوظة لهذه الشركة حتى الآن.</p>
                </div>
            @endif

            @foreach($invoices as $invoice)
            <div class="card mb-4 shadow-sm border-right-lg border-{{ $invoice->remaining_amount <= 0 ? 'success' : 'warning' }}">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <h5 class="mb-0 text-primary">رقم الفاتورة: #{{ $invoice->id }}</h5>
                            <small class="text-muted"><i class="far fa-calendar-alt ml-1"></i> التاريخ: {{ $invoice->created_at->format('Y-m-d') }}</small>
                        </div>
                        <div class="col-md-4 text-center">
                            @if($invoice->remaining_amount <= 0)
                                <span class="badge badge-success px-4 py-2" style="font-size: 1rem;">خالصة (تم السداد بالكامل)</span>
                            @else
                                <span class="badge badge-warning px-4 py-2" style="font-size: 1rem;">متبقي: {{ number_format($invoice->remaining_amount, 2) }} د.ل</span>
                            @endif
                        </div>
                        <div class="col-md-4 text-left">
                            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#payModal{{ $invoice->id }}">
                                <i class="fas fa-plus-circle ml-1"></i> تسوية دفعة
                            </button>
                            
                            <button class="btn btn-sm btn-dark ml-1" data-toggle="modal" data-target="#printModal{{ $invoice->id }}">
                                <i class="fas fa-print ml-1"></i> طباعة الفاتورة
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 text-center">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th style="width: 40%">الخدمة</th>
                                <th style="width: 20%">الإجراء</th>
                                <th style="width: 15%">العدد</th>
                                <th style="width: 25%">المبلغ المالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->service_name }}</td>
                                <td><span class="badge badge-light border">{{ $item->action }}</span></td>
                                <td>{{ $item->quantity }}</td>
                                <td class="font-weight-bold text-dark">{{ number_format($item->price, 2) }} د.ل</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-left font-weight-bold font-italic">المجموع الكلي:</td>
                                <td class="bg-success text-white font-weight-bold">{{ number_format($invoice->total_amount, 2) }} د.ل</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-left font-weight-bold">الواصل الإجمالي:</td>
                                <td class="text-primary font-weight-bold">{{ number_format($invoice->paid_amount, 2) }} د.ل</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="payModal{{ $invoice->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('finance.update_payment', $invoice->id) }}" method="POST">
                        @csrf
                        <div class="modal-content text-right">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">إضافة دفعة مالية جديدة - فاتورة #{{ $invoice->id }}</h5>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    المبلغ المتبقي حالياً: <strong>{{ number_format($invoice->remaining_amount, 2) }} د.ل</strong>
                                </div>
                                <div class="form-group">
                                    <label>أدخل المبلغ الواصل الجديد:</label>
                                    <input type="number" name="new_payment" class="form-control" step="0.01" max="{{ $invoice->remaining_amount }}" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">تحديث الحساب</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="printModal{{ $invoice->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="{{ route('finance.print', $invoice->id) }}" method="GET" target="_blank">
                        <div class="modal-content text-right">
                            <div class="modal-header bg-dark text-white">
                                <h5 class="modal-title"><i class="fas fa-print ml-2"></i>تخصيص بيانات الطباعة</h5>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">اسم محاسب عام الشركة:</label>
                                    <input type="text" name="accountant" class="form-control" placeholder="اكتب اسم المحاسب هنا..." required>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">اسم مشرف عام الشركة:</label>
                                    <input type="text" name="manager" class="form-control" placeholder="اكتب اسم المشرف هنا..." required>
                                </div>
                                <div class="alert alert-warning py-2 small">
                                    <i class="fas fa-info-circle ml-1"></i> سيتم عرض الخدمات المسجلة فقط في الفاتورة المطبوعة.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary px-4">توليد الفاتورة</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection