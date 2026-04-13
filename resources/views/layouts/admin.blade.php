<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title') </title>
  @include('admin.includes.header')

  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/custom_rtl.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/mycustomstyle.css') }}">

  {{-- بداية كود إصلاح الانعكاس الموحد --}}
<style>
    /* 1. إجبار عناوين البطاقات على جهة اليمين في كل النظام */
    .card-header {
        display: block !important; /* إلغاء نظام توزيع العناصر التلقائي */
        text-align: right !important;
    }

    .card-title {
        float: right !important; /* إجبار العنوان على اليمين */
        margin: 0 !important;
        padding: 5px 0;
        display: inline-block !important;
    }

    /* 2. ضبط أزرار الـ Header إذا وجدت (مثل أزرار التصغير أو الإغلاق) */
    .card-tools {
        float: left !important; /* نقل أدوات البطاقة لجهة اليسار */
        margin-right: auto;
        margin-left: 0;
    }

    /* 3. إصلاح الهوامش في النماذج (Forms) لتكون من اليمين */
    .form-group label {
        text-align: right !important;
        width: 100%;
        display: block;
    }

    /* 4. إجبار الأيقونات داخل العناوين على البقاء بجانب النص */
    .card-title i {
        margin-left: 5px;
        margin-right: 0;
    }
</style>
  {{-- نهاية كود الإصلاح --}}
  
</head>
<body class="hold-transition sidebar-mini rtl">
<div class="wrapper">

  @include('admin.includes.navbar')
  @include('admin.includes.sidebar')

  @include('admin.includes.content')

  @include('admin.includes.footer')

</div>

<script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // 1. كود إظهار تنبيه التأكيد عند الضغط على زر الحذف
        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف هذا السجل نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف الآن ✅',
                cancelButtonText: 'إلغاء ❌',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // 2. كود إظهار رسالة النجاح (منفصل تماماً ليعمل عند تحميل الصفحة)
        @if(session('success'))
            Swal.fire({
                title: 'تمت العملية بنجاح!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'حسناً',
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // 3. كود إظهار رسالة الخطأ
        @if(session('error'))
            Swal.fire({
                title: 'خطأ!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'حسناً'
            });
        @endif
    });
</script>
<script>
function confirmLogout() {
    // التأكد من أن مكتبة SweetAlert2 محملة
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم إنهاء الجلسة الحالية",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، تسجيل الخروج',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // إرسال الفورم المخفي
                document.getElementById('logout-form').submit();
            }
        });
    } else {
        // في حال لم تتحمل المكتبة، ينفذ الخروج مباشرة
        if (confirm('هل تريد تسجيل الخروج؟')) {
            document.getElementById('logout-form').submit();
        }
    }
}
</script>
@yield('js') 
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="contactModalLabel">
                    <i class="fas fa-headset ml-2"></i> الدعم الفني
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body text-center p-4">
                <div class="mb-4">
                    <h3 class="font-weight-bold text-dark">Albuazi_soft</h3>
                    <p class="text-muted">للدعم الفني والاستفسارات البرمجية</p>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="p-3 border rounded shadow-sm" style="background-color: #f8f9fa;">
                            <i class="fas fa-phone-alt fa-2x text-primary mb-2"></i>
                            <p class="small text-muted mb-1">اتصال مباشر</p>
                            <a href="tel:0912028008" class="h6 font-weight-bold d-block text-primary" style="direction: ltr;">0912028008</a>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="p-3 border rounded shadow-sm" style="background-color: #f8f9fa;">
                            <i class="fab fa-whatsapp fa-2x text-success mb-2"></i>
                            <p class="small text-muted mb-1">واتساب</p>
                            <a href="https://wa.me/218912028008" target="_blank" class="h6 font-weight-bold d-block text-success" style="direction: ltr;">0912028008</a>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <p class="small text-muted mb-0">
                        <i class="fas fa-map-marker-alt ml-1"></i> طرابلس - ليبيا
                    </p>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal" style="border-radius: 10px;">إغلاق</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>