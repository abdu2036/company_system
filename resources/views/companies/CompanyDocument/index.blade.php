@extends('layouts.admin')

@section('title', 'أرشيف الشركات')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        {{-- تحسين تصميم الهيدر ليكون أكثر تناسقاً مع نظام AdminLTE --}}
        <div class="card-header bg-info text-white" style="direction: rtl;">
            <h3 class="card-title" style="float: right; line-height: 2;">
                <i class="fas fa-archive ml-2"></i> أرشيف مستندات الشركات
            </h3>
            
            <div class="card-tools" style="float: left; width: 300px;">
                <div class="input-group input-group-sm">
                    <input type="text" id="tableSearch" class="form-control" placeholder="بحث سريع عن شركة...">
                    <div class="input-group-append">
                        <span class="input-group-text bg-white border-left-0">
                            <i class="fas fa-search text-info"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive text-right">
                <table class="table table-hover table-striped mb-0" id="archiveTable" style="direction: rtl;">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>اسم الشركة</th>
                            <th class="text-center">إحصائيات المستندات</th>
                            <th class="text-center">الحالة الإدارية</th>
                            <th class="text-center" style="width: 180px;">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $index => $company)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <span class="d-block font-weight-bold">{{ $company->name }}</span>
                                <small class="text-muted">كود الشركة: #{{ $company->id }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-pill badge-info py-2 px-3 shadow-sm">
                                    <i class="fas fa-file-pdf ml-1"></i> {{ $company->documents_count }} مستند
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="text-success small font-weight-bold">
                                    <i class="fas fa-check-double ml-1"></i> مراجعة ومؤرشفة
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 shadow-sm" 
                                        onclick="openDocumentsModal('{{ $company->id }}', '{{ $company->name }}')">
                                    <i class="fas fa-folder-open ml-1"></i> إدارة الملفات
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">لا يوجد شركات مسجلة في النظام حالياً</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('companies.CompanyDocument.archive_modal')
@endsection

{{-- استخدام القسم js ليتوافق مع الـ Layout الخاص بك --}}
@section('js')
<script>
    $(document).ready(function(){
        // البحث الفوري مع إخفاء/إظهار رسالة "لا يوجد نتائج"
        $("#tableSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#archiveTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function openDocumentsModal(companyId, companyName) {
        $('#modalTitle').html(`<i class="fas fa-building text-info ml-2"></i> مستندات شركة: <span class="text-dark">${companyName}</span>`);
        $('#documentsModal').data('company-id', companyId);
        
        // هيكل تحميل جذاب
        $('#documentsTableBody').html(`
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="spinner-border text-info" role="status"></div>
                    <div class="mt-2 text-muted small">جاري جلب الملفات من السيرفر...</div>
                </td>
            </tr>
        `);
        
        $('#documentsModal').modal('show');

        $.get(`/companies/${companyId}/documents`, function(data) {
            let rows = '';
            if(data.length > 0) {
                data.forEach(function(doc) {
                    // تحسين رابط المعاينة ليستخدم دالة asset لضمان المسار الصحيح
                    let fileUrl = `{{ asset('') }}${doc.file_path}`;
                    
                    rows += `
                    <tr>
                        <td class="text-right align-middle">
                            <div class="font-weight-bold">${doc.document_name}</div>
                            <div class="small text-muted italic">${doc.notes ? doc.notes : '---'}</div>
                        </td>
                        <td class="align-middle small">${doc.created_at}</td>
                        <td class="align-middle"><span class="badge badge-light border font-weight-normal">${doc.file_size}</span></td>
                        <td class="align-middle">
                            <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-primary shadow-sm rounded-circle" title="فتح الملف">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="50" class="opacity-50">
                        <p class="text-muted mt-2">لا توجد ملفات مرفوعة لهذه الشركة حتى الآن</p>
                    </td>
                </tr>`;
            }
            $('#documentsTableBody').hide().html(rows).fadeIn();
        }).fail(function() {
            $('#documentsTableBody').html('<tr><td colspan="4" class="text-center text-danger py-4 font-weight-bold">حدث خطأ أثناء الاتصال بالسيرفر!</td></tr>');
        });
    }

    function saveNewDocument() {
        let id = $('#documentsModal').data('company-id');
        let fileInput = $('#doc_file')[0].files[0];
        
        if(!fileInput) { 
            Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يجب اختيار ملف للرفع', confirmButtonText: 'حسناً' });
            return; 
        }

        let formData = new FormData();
        formData.append('file', fileInput);
        formData.append('document_name', $('#doc_name').val());
        formData.append('notes', $('#doc_notes').val());
        formData.append('_token', '{{ csrf_token() }}');

        // استخدام تأثير التحميل على الزر
        let btn = $('.btn-save-doc'); 
        let originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin ml-1"></i> جاري الرفع...');

        $.ajax({
            url: `/companies/${id}/documents/upload`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                // إظهار تنبيه نجاح باستخدام SweetAlert2
                Swal.fire({ icon: 'success', title: 'تم الحفظ!', text: 'تم رفع المستند بنجاح', timer: 2000, showConfirmButton: false });
                
                $('#doc_name, #doc_notes, #doc_file').val('');
                btn.prop('disabled', false).html(originalText);
                
                // إعادة تحميل الجدول داخل المودال
                openDocumentsModal(id, $('#modalTitle').text().replace('مستندات شركة: ', ''));
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'حدث خطأ غير متوقع';
                Swal.fire({ icon: 'error', title: 'فشل الرفع', text: errorMsg });
                btn.prop('disabled', false).html(originalText);
            }
        });
    }
</script>
@endsection