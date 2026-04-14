<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>نظام إدارة الشركات | تسجيل الدخول</title>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">

  <style>
    html, body {
        height: 100%;
        margin: 0;
    }

    body {
        font-family: 'Cairo', sans-serif;
        background-color: #f0f2f5;
    }

    /* 🔥 هذا هو الحل الحقيقي */
    .wrapper-center {
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-container {
        width: 100%;
        max-width: 900px;
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }

    .login-header-section {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        padding: 40px;
        width: 40%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .login-header-section img {
        width: 120px;
        margin-bottom: 20px;
        filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2));
    }

    .login-form-section {
        padding: 50px;
        width: 60%;
        background: white;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px 20px;
        height: auto;
        border: 1px solid #ddd;
    }

    .btn-primary {
        border-radius: 10px;
        padding: 12px;
        font-weight: 700;
        background: #4e73df;
        border: none;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: #224abe;
        transform: translateY(-2px);
    }

    .input-group-text {
        border-radius: 0 10px 10px 0 !important;
        background-color: #f8f9fa;
    }

    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
            max-width: 400px;
            margin: 20px;
        }
        .login-header-section, .login-form-section {
            width: 100%;
        }
        .login-header-section { padding: 30px; }
    }
  </style>
</head>

<body>

<div class="wrapper-center">
    <div class="login-container">
        
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
            <h3 class="font-weight-bold">أهلاً بعودتك!</h3>
            <p>نظام إدارة الشركات يرحب بكم</p>
        </div>

        <div class="login-form-section">
            <div class="text-center mb-4">
                <h4 class="text-dark font-weight-bold">تسجيل الدخول</h4>
                <p class="text-muted small">يرجى إدخال بياناتك للوصول إلى لوحة التحكم</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="small font-weight-bold">البريد الإلكتروني</label>
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="example@mail.com" required autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold">كلمة المرور</label>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                        <label class="custom-control-label small" for="remember">تذكرني</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="small text-primary font-weight-bold">نسيت كلمة المرور؟</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block shadow">تسجيل الدخول</button>
            </form>

            <div class="text-center mt-4">
                <p class="mb-0 small">
                    ليس لديك حساب؟
                    <a href="{{ route('register') }}" class="text-primary font-weight-bold">إنشاء حساب جديد</a>
                </p>
            </div>

            <div class="text-center mt-5">
                <p class="small text-muted">© 2026 جميع الحقوق محفوظة | نظام إدارة الشركات</p>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>

</body>
</html>