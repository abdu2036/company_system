<div class="modal fade" id="documentsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl border-0">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalTitle">أرشيف المستندات</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="direction: rtl; text-align: right;">
                
                <div class="row bg-light p-3 mb-4 rounded border">
                    <div class="col-md-4">
                        <label>اسم المستند</label>
                        <input type="text" id="doc_name" class="form-control" placeholder="مثلاً: عقد تأسيس شركة">
                    </div>
                    <div class="col-md-3">
                        <label>اختر الملف</label>
                        <input type="file" id="doc_file" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>ملاحظات</label>
                        <input type="text" id="doc_notes" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button class="btn btn-success btn-block" onclick="saveNewDocument()">حفظ</button>
                    </div>
                </div>

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>المستند</th>
                            <th>تاريخ الرفع</th>
                            <th>الحجم</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="documentsTableBody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>