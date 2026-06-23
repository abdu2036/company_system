@extends('layouts.admin')

@section('title', 'إدارة الخزينة والصناديق')

@section('content')
<section class="content text-right" dir="rtl" style="padding-top: 20px;">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success border-right border-success m-2">
                <i class="fas fa-check-circle ml-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-right border-danger m-2">
                <i class="fas fa-times-circle ml-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="small-box bg-success shadow-sm">
                    <div class="inner">
                        <h3>{{ number_format($currentBalance, 2) }} <small style="font-size: 1.2rem;" class="text-white">د.ل</small></h3>
                        <p class="font-weight-bold">السيولة المتوفرة في درج الخزينة الآن</p>
                    </div>
                    <div class="icon"><i class="fas fa-cash-register"></i></div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-primary h-100 d-flex align-items-center justify-content-center bg-light">
                    <div class="card-body w-100 text-center p-4">
                        <h5 class="mb-3 font-weight-bold"><i class="fas fa-exchange-alt ml-1"></i> إجراء حركة ماليّة على الخزينة</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-outline-success btn-block btn-lg shadow-sm" data-toggle="modal" data-target="#depositModal">
                                    <i class="fas fa-arrow-alt-circle-down ml-1"></i> إيداع إيراد / سيولة نقديّة
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-danger btn-block btn-lg shadow-sm" data-toggle="modal" data-target="#withdrawModal">
                                    <i class="fas fa-hand-holding-usd ml-1"></i> تسليم سيولة لمدير الشركة (سحب)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary shadow-sm mt-3">
            <div class="card-header bg-white">
                <h3 class="card-title float-left font-weight-bold">
                    <i class="fas fa-history ml-1 text-primary"></i> كشف الحركات والتدفقات النقدية للخزينة
                </h3>
            </div>
            
            <div class="card-body p-0">
                <table class="table table-hover table-bordered text-center mb-0" style="width: 100%;">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th>#</th>
                            <th>نوع الحركة</th>
                            <th>المبلغ</th>
                            <th>المُسلِّم</th>
                            <th>المُستلِّم</th>
                            <th style="width: 25%;">البيان / Mلاحظات</th>
                            <th>التاريخ</th>
                            <th>خيارات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    @if($transaction->type == 'deposit')
                                        <span class="badge bg-success-light text-success p-2">
                                            <i class="fas fa-plus-circle ml-1"></i> إيداع بالخزينة
                                        </span>
                                    @else
                                        <span class="badge bg-danger-light text-danger p-2">
                                            <i class="fas fa-minus-circle ml-1"></i> سحب للإدارة
                                        </span>
                                    @endif
                                        </td>
                                <td class="align-middle font-weight-bold {{ $transaction->type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type == 'deposit' ? '+' : '-' }} {{ number_format($transaction->amount, 2) }} د.ل
                                </td>
                                <td class="align-middle text-muted small">{{ $transaction->delivered_by ?? 'غير محدد' }}</td>
                                <td class="align-middle text-muted small"><strong>{{ $transaction->received_by ?? 'غير محدد' }}</strong></td>
                                <td class="align-middle text-right small">{{ $transaction->notes }}</td>
                                <td class="align-middle text-muted small">{{ $transaction->transaction_date }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('treasury.print_receipt', $transaction->id) }}" target="_blank" class="btn btn-sm btn-info shadow-sm" title="طباعة الوصل">
                                        <i class="fas fa-print"></i> طباعة
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center p-4 text-muted">
                                    <i class="fas fa-folder-open d-block mb-2 fa-2x"></i> لا توجد أي حركات مسجلة في الخزينة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
                <div class="card-footer bg-white d-flex justify-content-center">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

<div class="modal fade text-right" id="depositModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-arrow-alt-circle-down ml-1"></i> إيداع مالي جديد في الخزينة</h5>
                <button type="button" class="close text-white mr-auto ml-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('treasury.deposit') }}" method="POST" id="depositForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">المبلغ المراد إيداعه (دينار ليبي): <span class="text-danger">*</span></label>
                        <input type="number" name="amount" step="0.01" class="form-control form-control-lg" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">اسم الشخص المُسلِّم للمبلغ: <span class="text-danger">*</span></label>
                        <input type="text" name="delivered_by" class="form-control" placeholder="مثال: موظف المبيعات، أو فرع حدائق غنيمة" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">البيان / ملاحظات الإيداع:</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="اكتب تفاصيل الإيراد المستلم هنا..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save ml-1"></i> تأكيد الإيداع في الصندوق</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade text-right" id="withdrawModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-hand-holding-usd ml-1"></i> تسليم سيولة لمدير الشركة (سحب)</h5>
                <button type="button" class="close text-white mr-auto ml-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('treasury.withdraw') }}" method="POST" id="withdrawForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning border-right border-warning small">
                        <i class="fas fa-exclamation-triangle ml-1"></i> تنبيه: هذه العملية ستخصم المبلغ من رصيد الخزينة المتوفر مباشرة دون تسجيله كمصروف تشغيلي للشركة.
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">المبلغ المراد تسليمه للمدير (دينار ليبي): <span class="text-danger">*</span></label>
                        <input type="number" name="amount" step="0.01" class="form-control form-control-lg border-danger text-danger font-weight-bold" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">اسم المدير / المستلم الفعلي للسيولة: <span class="text-danger">*</span></label>
                        <input type="text" name="received_by" class="form-control" placeholder="مثال: المدير العام، الأستاذ سالم عبدالله" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">البيان / تصفية الحساب:</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="مثال: تصفية وسحب صافي سيولة الخزينة اليومية المتاحة"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" onclick="confirmWithdrawal()" class="btn btn-danger"><i class="fas fa-check ml-1"></i> اعتماد حركة السحب الفوري</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmWithdrawal() {
    const form = document.getElementById('withdrawForm');
    const amount = form.elements['amount'].value;
    const receiver = form.elements['received_by'].value;

    if(!amount || !receiver) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ في البيانات',
            text: 'الرجاء ملء الحقول الإلزامية أولاً لقيمة المبلغ والمستلم.',
            confirmButtonText: 'حسناً'
        });
        return;
    }

    Swal.fire({
        title: 'تأكيد تصفية وسحب سيولة؟',
        text: `هل أنت متأكد من تسليم مبلغ قدره (${amount} د.ل) إلى (${receiver})؟ سيتم خصم القيمة من صندوق الخزينة فوراً!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، اعتمد السحب والتسليم',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'جاري تحديث الصناديق...',
                didOpen: () => { Swal.showLoading() }
            });
            form.submit();
        }
    });
}
</script>

<style>
.bg-success-light { background-color: rgba(40, 167, 69, 0.15); }
.bg-danger-light { background-color: rgba(220, 53, 69, 0.15); }
</style>