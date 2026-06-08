
<div class="modal fade text-right" id="addCategoryModal" tabindex="-1" role="dialog" aria-hidden="true" style="direction: rtl;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-tags mr-2 text-warning"></i> إضافة بند مصروفات جديد للشركة
                </h5>
                <button type="button" class="close text-white ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('expenses.categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">اسم بند المصروف:</label>
                        <input type="text" name="name" class="form-control text-right" placeholder="مثال: رسوم غرف تجارية، رسوم بلدية، أتعاب..." required>
                        <small class="text-muted">هذا البند سيظهر لاحقاً في قائمة المصروفات الخاصة بهذه الشركة فقط.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-secondary font-weight-bold">حفظ البند المالي</button>
                </div>
            </form>
        </div>
    </div>
</div>