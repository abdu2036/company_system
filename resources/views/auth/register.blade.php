<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>نظام إدارة الشركات | إنشاء حساب</title>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
  
  <style>
    body { font-family: 'Cairo', sans-serif; background-color: #f0f2f5; height: 100vh; margin: 0; display: flex; align-items: center; justify-content: center; }
    
    .login-container { 
        width: 100%; 
        max-width: 900px; 
        background: #fff; 
        border-radius: 20px; 
        overflow: hidden; 
        display: flex; 
        /* --- التعديل الجوهري لظهور القسم الأخضر على اليمين --- */
        
        /* ---------------------------------------------------- */
        box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
        margin: 15px; 
    }
    
    .login-header-section { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; padding: 40px; width: 40%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
    .login-header-section img { width: 100px; margin-bottom: 20px; filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2)); }
    .login-form-section { padding: 40px; width: 60%; background: white; text-align: right; }
    .form-control { border-radius: 10px; padding: 10px 15px; height: auto; border: 1px solid #ddd; }
    .btn-success { border-radius: 10px; padding: 10px; font-weight: 700; background: #28a745; border: none; transition: all 0.3s; }
    .btn-success:hover { background: #1e7e34; transform: translateY(-2px); }
    
    /* تعديل حواف الأيقونات لتناسب الـ RTL */
    .input-group-text { border-radius: 10px 0 0 10px !important; background-color: #f8f9fa; }
    
    @media (max-width: 768px) { .login-container { flex-direction: column; max-width: 450px; } .login-header-section, .login-form-section { width: 100%; } }
  </style>
</head>
<body class="hold-transition">

<div class="login-container shadow-lg">
  
    <div class="login-header-section">
        <img src="{{ asset('assets/admin/dist/img/2026.png') }}" 
         alt="A-Soft Icon" 
         class="brand-image img-circle elevation-3 shadow-lg" 
         style="opacity: 1; 
                float: none; 
                margin: 0 auto; 
                display: block; 
                max-height: 70px; /* زيادة حجم الصورة بشكل ملحوظ لبروزها */
                border: 2px solid #fff; /* إضافة إطار أبيض لزيادة البروز */
                background-color: #fff; 
                transition: transform 0.3s ease; /* إضافة تأثير حركي بسيط */
                ">
        <h3 class="font-weight-bold">انضم إلينا!</h3>
        <p>ابدأ بتنظيم بيانات شركاتك الآن</p>
    </div>

    <div class="login-form-section">
        <div class="text-center mb-4">
            <h4 class="text-dark font-weight-bold">إنشاء حساب جديد</h4>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group mb-2 text-right">
                <label class="small font-weight-bold">الاسم الكامل</label>
                <div class="input-group">
                    <input type="text" name="name" class="form-control" placeholder="أدخل اسمك" required autofocus>
                    <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
                </div>
            </div>

            <div class="form-group mb-2 text-right">
                <label class="small font-weight-bold">البريد الإلكتروني</label>
                <div class="input-group">
                    <input type="email" name="email" class="form-control" placeholder="mail@example.com" required>
                    <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 text-right">
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">كلمة المرور</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="******" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">تأكيد الكلمة</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="******" required>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-block shadow-sm">تسجيل الحساب</button>
        </form>

        <div class="text-center mt-3">
            <p class="mb-0 small">لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-success font-weight-bold">دخول</a></p>
        </div>
    </div>
</div>
</body>
</html>