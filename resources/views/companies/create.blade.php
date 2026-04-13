@extends('layouts.admin')

@section('title', 'إضافة شركة جديدة')

@section('content_header')
<h1 class="text-right">إضافة شركة جديدة</h1>
@stop

@section('content')

<div class="card card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title float-right">نموذج تسجيل الشركات المتكامل</h3>
    </div>

    <div class="card-body" style="direction: rtl;">
        <div class="row mb-4">
            <div class="col-12">
                <div class="progress progress-sm mb-2" style="direction: rtl; height: 10px !important;">
                    <div id="progress-bar" class="progress-bar bg-success" role="progressbar"
                        style="width: 20%; float: right;"></div>
                </div>
                <div class="d-flex justify-content-between mb-3" style="direction: rtl;">
    <small id="step-t-1" class="text-bold text-success">1. البيانات الأساسية</small>
    <small id="step-t-2" class="text-muted">2. بيانات السجل</small>
    <small id="step-t-3" class="text-muted">3. التراخيص</small>
    <small id="step-t-4" class="text-muted">4. الغرفة التجارية</small>
    <small id="step-t-5" class="text-muted">5. سجل المستوردين</small>
</div>
            </div>
        </div>

        <div id="info-bar" class="alert d-none mb-4 shadow-sm"
            style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-right: 5px solid #007bff; color: #333;">
            <div class="row text-right">
                <div class="col-md-4"><strong>اسم الشركة:</strong> <span id="top_name"
                        class="text-primary font-weight-bold">---</span></div>
                <div class="col-md-4"><strong>النشاط:</strong> <span id="top_activity"
                        class="text-primary font-weight-bold">---</span></div>
                <div class="col-md-4"><strong>العنوان:</strong> <span id="top_address"
                        class="text-primary font-weight-bold">---</span></div>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"
                style="direction: rtl; text-align: right;">
                <i class="fas fa-check-circle ml-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert"
                style="direction: rtl; text-align: right;">
                <i class="fas fa-exclamation-triangle ml-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning shadow-sm" style="direction: rtl; text-align: right;">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" id="companyForm">
            @csrf
            <div class="step-content" id="step-1">
                <h5 class="text-right border-bottom pb-2 mb-3">1. البيانات الأساسية</h5>
                <div class="row text-right">
                    <div class="form-group col-md-6">
                        <label>اسم الشركة<span style="color: red">*</span></label>
                        <input type="text" name="name" id="main_name" class="form-control" required
                            oninput="syncData(); hideError(this);" value="{{ old('name') }}">
                        <span class="text-danger error-msg d-none" id="error-main_name"
                            style="font-size: 13px; font-weight: bold; display: block; margin-top: 5px;">هذا الحقل
                            مطلوب</span>
                    </div>

                    <div class="form-group col-md-6">
                        <label>نشاط الشركة<span style="color: red">*</span></label>
                        <input type="text" name="activity" id="main_activity" class="form-control" required
                            oninput="syncData(); hideError(this);" value="{{ old('activity') }}">
                        <span class="text-danger error-msg d-none" id="error-main_activity"
                            style="font-size: 13px; font-weight: bold; display: block; margin-top: 5px;">هذا الحقل
                            مطلوب</span>
                    </div>

                    <div class="form-group col-12">
                        <label>عنوان الشركة<span style="color: red">*</span></label>
                        <textarea name="address" id="main_address" class="form-control" rows="2" required
                            oninput="syncData(); hideError(this);">{{ old('address') }}</textarea>
                        <span class="text-danger error-msg d-none" id="error-main_address"
                            style="font-size: 13px; font-weight: bold; display: block; margin-top: 5px;">هذا الحقل
                            مطلوب</span>
                    </div>
                </div>
                <div class="d-flex justify-content-start mt-4">
                    <button type="button" onclick="nextStep(2)" class="btn btn-primary px-4">التالي</button>
                </div>
            </div>
            <div class="step-content d-none" id="step-2">
                <h5 class="text-right border-bottom pb-2 mb-4 text-info">
                    <i class="fas fa-file-alt ml-2"></i> 2. بيانات السجل التجاري
                </h5>

                <div class="row text-right" style="direction: rtl;">
                    <div class="form-group col-md-4">
                        <label>رقم السجل التجاري <span class="text-danger">*</span></label>
                        <input type="text" name="cr_number" id="cr_number" class="form-control" required
                            oninput="hideError(this)" value="{{ old('cr_number') }}">
                        <span class="text-danger error-msg d-none" id="error-cr_number"
                            style="font-size: 12px; font-weight: bold;">هذا الحقل مطلوب</span>
                    </div>

                    <div class="form-group col-md-4">
                        <label>رقم الهاتف <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" required
                            oninput="hideError(this)" value="{{ old('phone') }}">
                        <span class="text-danger error-msg d-none" id="error-phone"
                            style="font-size: 12px; font-weight: bold;">هذا الحقل مطلوب</span>
                    </div>

                    <div class="form-group col-md-4">
                        <label>اسم المفوض <span class="text-danger">*</span></label>
                        <input type="text" name="representative_name" id="representative_name" class="form-control"
                            required oninput="hideError(this)" value="{{ old('representative_name') }}">
                        <span class="text-danger error-msg d-none" id="error-representative_name"
                            style="font-size: 12px; font-weight: bold;">هذا الحقل مطلوب</span>
                    </div>

                    <div class="form-group col-md-6">
                        <label>تاريخ إصدار السجل <span class="text-danger">*</span></label>
                        <input type="date" name="cr_issue_date" id="cr_issue_date" class="form-control text-right"
                            required oninput="hideError(this)" value="{{ old('cr_issue_date') }}">
                        <span class="text-danger error-msg d-none" id="error-cr_issue_date"
                            style="font-size: 12px; font-weight: bold;">هذا الحقل مطلوب</span>
                    </div>

                    <div class="form-group col-md-6">
                        <label>تاريخ انتهاء السجل <span class="text-danger">*</span></label>
                        <input type="date" name="cr_expiry_date" id="cr_expiry_date" class="form-control text-right"
                            required oninput="hideError(this)" value="{{ old('cr_expiry_date') }}">
                        <span class="text-danger error-msg d-none" id="error-cr_expiry_date"
                            style="font-size: 12px; font-weight: bold;">هذا الحقل مطلوب</span>
                    </div>
                    <div class="form-group col-md-12">
                        <label>إرفاق السجل التجاري <span style="color: red">*</span></label>
                        <input type="file" id="cr_upload" class="form-control"
                            onchange="uploadFile(this, 'commercial_register')">

                        <div id="upload_status" class="mt-2"></div>

                        <input type="hidden" name="temp_file_path" id="temp_file_path">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" onclick="nextStep(1)" class="btn btn-outline-secondary px-4">السابق</button>
                    <button type="button" onclick="nextStep(3)" class="btn btn-primary px-4">التالي</button>
                </div>
            </div>

            <div class="step-content d-none" id="step-3">
                <h5 class="text-right border-bottom pb-2 mb-3 text-info">3. بيانات التراخيص 📄</h5>
                <div class="row text-right">
                    <div class="form-group col-md-6"><label>رقم الترخيص العام</label><input type="text"
                            name="license_number" class="form-control" value="{{ old('license_number') }}"></div>
                    <div class="form-group col-md-6"><label>الرقم الضريبي</label><input type="text" name="tax_number"
                            class="form-control" value="{{ old('tax_number') }}"></div>
                    {{-- قسم التواريخ --}}
                    <div class="col-md-4">
                        <label>تاريخ إصدار الترخيص *</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control"  {{ old('issue_date') }}
                            style="background-color: #e9ecef;">
                    </div>

                    <div class="col-md-4">
                        <label>مدة صلاحية الترخيص *</label>
                        <select id="validity_period" name="validity_period" class="form-control"  >
                            <option value="">-- اختر المدة --</option>
                            <option value="1">سنة واحدة</option>
                            <option value="2">سنتين</option>
                            <option value="3">ثلاث سنوات</option>

                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>تاريخ الانتهاء *</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="form-control" {{ old('expiry_date') }} readonly
                            style="background-color: #e9ecef;">
                    </div>
                    <div class="form-group col-md-10">
                        <label>إرفاق مستند الترخيص</label>
                        <input type="file" id="license_upload" class="form-control"
                            onchange="uploadFile(this, 'license')">

                        <div id="license_upload_status" class="mt-2"></div>

                        <input type="hidden" name="license_temp_path" id="license_temp_path">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" onclick="nextStep(2)" class="btn btn-outline-secondary px-4">السابق</button>
                    <button type="button" onclick="nextStep(4)" class="btn btn-primary px-4">التالي</button>
                </div>
            </div>

           <div class="step-content d-none" id="step-4">
    <h5 class="text-right border-bottom pb-2 mb-3 text-info">4. بيانات الغرفة التجارية 🏛️</h5>
    <div class="row text-right">
        <div class="form-group col-md-12">
            <label>رقم عضوية الغرفة التجارية</label>
            <input type="text" name="chamber_number" class="form-control" value="{{ old('chamber_number') }}">
        </div>
        
        <div class="col-md-4">
            <label>تاريخ إصدار الغرفة</label>
            <input type="date" name="chamber_issue_date" id="chamber_issue_date" class="form-control" value="{{ old('chamber_issue_date') }}">
        </div>

        <div class="col-md-4">
            <label>مدة الصلاحية</label>
            <select id="chamber_period" name="chamber_period" class="form-control">
                <option value="">-- اختر المدة --</option>
                <option value="1">سنة واحدة</option>
                <option value="2">سنتين</option>
                <option value="3">ثلاث سنوات</option>
            </select>
        </div>

        <div class="col-md-4">
            <label>تاريخ انتهاء الغرفة</label>
            <input type="date" name="chamber_expiry_date" id="chamber_expiry_date" class="form-control" readonly style="background-color: #e9ecef;">
        </div>

        <div class="form-group col-md-10 mt-3">
            <label>إرفاق مستند الغرفة</label>
            <input type="file" id="chamber_upload" class="form-control" onchange="uploadFile(this, 'chamber')">
            <div id="chamber_upload_status" class="mt-2"></div>
            <input type="hidden" name="chamber_temp_path" id="chamber_temp_path">
        </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <button type="button" onclick="nextStep(3)" class="btn btn-outline-secondary px-4">السابق</button>
        <button type="button" onclick="nextStep(5)" class="btn btn-primary px-4">التالي</button>
    </div>
</div>

           <div class="step-content d-none" id="step-5">
    <h5 class="text-right border-bottom pb-2 mb-3 text-info">5. سجل المستوردين 🚢</h5>
    <div class="row text-right">
        <div class="form-group col-md-12">
            <label>رقم قيد المستوردين</label>
            <input type="text" name="importer_number" class="form-control" value="{{ old('importer_number') }}">
        </div>

        <div class="col-md-4">
            <label>تاريخ إصدار السجل</label>
            <input type="date" name="importer_issue_date" id="importer_issue_date" class="form-control" value="{{ old('importer_issue_date') }}">
        </div>

        <div class="col-md-4">
            <label>مدة الصلاحية</label>
            <select id="importer_period" name="importer_period" class="form-control">
                <option value="">-- اختر المدة --</option>
                <option value="1">سنة واحدة</option>
                <option value="2">سنتين</option>
                <option value="3">ثلاث سنوات</option>
            </select>
        </div>

        <div class="col-md-4">
            <label>تاريخ انتهاء الصلاحية</label>
            <input type="date" name="importer_expiry_date" id="importer_expiry_date" class="form-control" readonly style="background-color: #e9ecef;">
        </div>

        <div class="form-group col-md-10 mt-3">
            <label>إرفاق مستند سجل المستوردين</label>
            <input type="file" id="importer_upload" class="form-control" onchange="uploadFile(this, 'importer')">
            <div id="importer_upload_status" class="mt-2"></div>
            <input type="hidden" name="importer_temp_path" id="importer_temp_path">
        </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <button type="button" onclick="nextStep(4)" class="btn btn-outline-secondary px-4">السابق</button>
        <button type="submit" class="btn btn-success px-5 font-weight-bold shadow">حفظ نهائي لكافة البيانات</button>
    </div>
</div>
        </form>
    </div>
</div>
@stop
@section('js')
<script>
    $(document).ready(function () {

        // --- 1. دالة موحدة وحقن ذكي لحساب تاريخ الانتهاء ---
        function calculateExpiry(issueSelector, periodSelector, expirySelector) {
            let issueDateVal = $(issueSelector).val();
            
            // جلب عدد السنوات من القائمة المنسدلة، وإذا لم تكن موجودة (مثل السجل التجاري) نفترض سنة واحدة
            let yearsToAdd = 1; 
            if (periodSelector && $(periodSelector).length > 0) {
                yearsToAdd = parseInt($(periodSelector).val()) || 0;
            }

            if (issueDateVal && yearsToAdd > 0) {
                let date = new Date(issueDateVal);
                date.setFullYear(date.getFullYear() + yearsToAdd);
                
                // تنسيق التاريخ ليكون YYYY-MM-DD
                let day = ("0" + date.getDate()).slice(-2);
                let month = ("0" + (date.getMonth() + 1)).slice(-2);
                let year = date.getFullYear();
                let expiryDate = year + "-" + month + "-" + day;
                
                $(expirySelector).val(expiryDate);
                
                // إخفاء التنبيهات الحمراء إن وجدت
                $(expirySelector).removeClass('is-invalid');
                let errorId = '#error-' + expirySelector.replace('#', '');
                $(errorId).addClass('d-none');
            }
        }

        // --- 2. ربط الأحداث لكل قسم (تحديث تلقائي عند تغيير التاريخ أو المدة) ---

        // أ- السجل التجاري (الخطوة 2)
        $('#cr_issue_date').on('change', function () {
            calculateExpiry('#cr_issue_date', null, '#cr_expiry_date');
        });

        // ب- التراخيص (الخطوة 3)
        $('#issue_date, #validity_period').on('change', function () {
            calculateExpiry('#issue_date', '#validity_period', '#expiry_date');
        });

        // ج- الغرفة التجارية (الخطوة 4) - المضاف حديثاً
        $('#chamber_issue_date, #chamber_period').on('change', function () {
            calculateExpiry('#chamber_issue_date', '#chamber_period', '#chamber_expiry_date');
        });

        // د- سجل المستوردين (الخطوة 5) - المضاف حديثاً
        $('#importer_issue_date, #importer_period').on('change', function () {
            calculateExpiry('#importer_issue_date', '#importer_period', '#importer_expiry_date');
        });


        // --- 3. وظيفة رفع الملفات الموحدة ---
        window.uploadFile = function (input, type) {
            let file = input.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append('file', file);
            formData.append('type', type);

            // تحديد مكان عرض الحالة والحقل المخفي
            let statusDivId = (type === 'commercial_register') ? 'upload_status' : type + '_upload_status';
            let hiddenInputId = (type === 'commercial_register') ? 'temp_file_path' : type + '_temp_path';

            let statusDiv = $('#' + statusDivId);
            statusDiv.html('<span class="text-info small">جاري الرفع... ⏳</span>');

            fetch('{{ url("/upload-temp") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $('#' + hiddenInputId).val(data.temp_path);
                    statusDiv.html('<span class="text-success small">✅ تم الرفع بنجاح</span>');
                } else {
                    statusDiv.html('<span class="text-danger small">❌ فشل: ' + data.message + '</span>');
                }
            })
            .catch(error => {
                statusDiv.html('<span class="text-danger small">❌ خطأ في الاتصال</span>');
            });
        };

        // --- 4. إدارة التنقل (Stepper) ---
        window.nextStep = function (step) {
            let currentStepId = parseInt($('.step-content:not(.d-none)').attr('id').replace('step-', ''));

            if (step > currentStepId) {
                let isStepValid = true;
                $(`#step-${currentStepId} [required]`).each(function () {
                    if (!$(this).val() || $(this).val().trim() === "") {
                        $(this).addClass('is-invalid');
                        $(`#error-${$(this).attr('id')}`).removeClass('d-none');
                        isStepValid = false;
                    }
                });
                if (!isStepValid) return;
            }

            if (step > 1) $('#info-bar').removeClass('d-none');
            else $('#info-bar').addClass('d-none');

            $('.step-content').addClass('d-none');
            $(`#step-${step}`).removeClass('d-none');

            $('#progress-bar').css('width', (step * 20) + '%');

            for (let i = 1; i <= 5; i++) {
                let el = $(`#step-t-${i}`);
                if (i <= step) el.addClass('text-success text-bold').removeClass('text-muted');
                else el.removeClass('text-success text-bold').addClass('text-muted');
            }
            syncData();
        };

        window.syncData = function () {
            $('#top_name').text($('#main_name').val() || '---');
            $('#top_activity').text($('#main_activity').val() || '---');
            $('#top_address').text($('#main_address').val() || '---');
        };

        window.hideError = function (input) {
            let $input = $(input);
            if ($input.val() && $input.val().trim() !== "") {
                $input.removeClass('is-invalid');
                $(`#error-${$input.attr('id')}`).addClass('d-none');
            }
        };
    });
</script>
@stop