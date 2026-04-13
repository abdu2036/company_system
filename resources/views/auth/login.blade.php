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
    body { font-family: 'Cairo', sans-serif; }
    .login-page { background: #f4f6f9; }
    .card { border-radius: 15px; border-top: 5px solid #007bff; }
    .input-group-text { background-color: #f8f9fa; }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>نظام إدارة</b> الشركات</a>
  </div>
  <div class="card shadow-lg">
    <div class="card-body login-card-body">
      <p class="login-box-msg font-weight-bold">أهلاً بك مجدداً! يرجى تسجيل الدخول</p>

      <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="كلمة المرور" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">تذكرني</label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">دخول</button>
          </div>
          </div>
      </form>

      <hr>
      <p class="mb-1 text-center">
        <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
      </p>
      <p class="mb-0 text-center">
        <span>ليس لديك حساب؟ </span>
        <a href="{{ route('register') }}" class="text-center font-weight-bold">إنشاء حساب جديد</a>
      </p>
    </div>
    </div>
</div>
<script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>
</body>
</html>