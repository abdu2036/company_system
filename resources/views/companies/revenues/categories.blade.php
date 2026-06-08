<div class="modal fade text-right" id="addCategoryModal" tabindex="-1" role="dialog" aria-hidden="true" style="direction: rtl;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-tags mr-2 text-warning"></i> إضافة بند إيرادات جديد للنظام
                </h5>
                <button type="button" class="close text-white ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('revenues.categories.store') }}" method="POST" id="add-category-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">اسم بند الإيراد:</label>
                        <input type="text" name="name" id="category-name-input" class="form-control text-right" placeholder="مثال: إيراد خدمات، استشارات..." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success font-weight-bold" id="submit-btn">
                        <span class="btn-text">حفظ البند المالي</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('add-category-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const btn = document.getElementById('submit-btn');
    const formData = new FormData(form);
    
    // إظهار حالة التحميل
    btn.disabled = true;
    btn.innerHTML = 'جاري الحفظ...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // تحديث كافة القوائم المنسدلة في الصفحة
            const selects = document.querySelectorAll('select[name^="categories["]');
            selects.forEach(select => {
                const option = new Option(data.category.name, data.category.id);
                select.add(option);
                select.value = data.category.id; // اختيار البند المضاف تلقائياً
            });
            
            $('#addCategoryModal').modal('hide');
            form.reset();
            
            Swal.fire({
                icon: 'success',
                title: 'تمت الإضافة!',
                text: 'تم إضافة بند الإيرادات بنجاح.',
                confirmButtonColor: '#28a745'
            });
        } else {
            throw new Error(data.message || 'حدث خطأ غير معروف');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'ربما هذا البند موجود مسبقاً، يرجى التأكد من الاسم.',
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'حفظ البند المالي';
    });
});
</script>