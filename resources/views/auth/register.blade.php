<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>نظام إدارة الشركات | تسجيل حساب جديد</title>

  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
  
  <style>
    body { font-family: 'Cairo', sans-serif; }
    .register-page { background: #f4f6f9; }
    .card { border-radius: 15px; border-top: 5px solid #28a745; /* لون أخضر للتمييز عن الدخول */ }
    .input-group-text { background-color: #f8f9fa; width: 45px; justify-content: center; }
  </style>
</head>
<body class="hold-transition register-page">
<div class="register-box" style="width: 400px;">
  <div class="register-logo">
    <a href="#"><b>نظام إدارة</b> الشركات</a>
  </div>

  <div class="card shadow-lg">
    <div class="card-body register-card-body">
      <p class="login-box-msg font-weight-bold">إنشاء حساب  جديد</p>

      <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="input-group mb-3 text-right">
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="الاسم الكامل" value="{{ old('name') }}" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user text-success"></span>
            </div>
          </div>
          @error('name')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="البريد الإلكتروني" value="{{ old('email') }}" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope text-success"></span>
            </div>
          </div>
          @error('email')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="كلمة المرور" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock text-success"></span>
            </div>
          </div>
          @error('password')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-check-double text-success"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-success btn-block shadow-sm font-weight-bold">
               إتمام التسجيل <i class="fas fa-user-plus mr-1"></i>
            </button>
          </div>
        </div>
      </form>

      <hr>
      <div class="text-center">
        <p class="mb-0">
            <span>لديك حساب بالفعل؟</span>
            <a href="{{ route('login') }}" class="font-weight-bold text-primary">تسجيل الدخول</a>
        </p>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>
</body>
</html>