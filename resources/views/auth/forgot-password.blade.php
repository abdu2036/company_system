<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>نظام إدارة الشركات | استعادة كلمة المرور</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
  <style>
    body { font-family: 'Cairo', sans-serif; background: #f4f6f9; }
    .card { border-radius: 15px; border-top: 5px solid #17a2b8; }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo"><b>نظام إدارة</b> الشركات</div>
  <div class="card shadow-lg">
    <div class="card-body login-card-body">
      <p class="login-box-msg font-weight-bold">استعادة كلمة المرور</p>
      
      @if (session('status'))
          <div class="alert alert-success text-center mb-3">
              {{ session('status') }}
          </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="البريد الإلكتروني" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
          @error('email')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
          @enderror
        </div>
        <button type="submit" class="btn btn-info btn-block text-white font-weight-bold">إرسال رابط الاستعادة</button>
      </form>

      <p class="mt-3 mb-1 text-center">
        <a href="{{ route('login') }}" class="text-secondary"><i class="fas fa-arrow-right"></i> العودة للدخول</a>
      </p>
    </div>
  </div>
</div>
</body>
</html>