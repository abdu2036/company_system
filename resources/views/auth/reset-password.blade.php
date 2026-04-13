<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>نظام إدارة الشركات | تعيين كلمة المرور</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
  <style>
    body { font-family: 'Cairo', sans-serif; background-color: #f4f6f9; }
    .login-box { width: 400px; }
    .card { border-radius: 15px; border-top: 5px solid #007bff; }
    .btn-primary { background-color: #007bff; border: none; }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>نظام إدارة</b> الشركات</a>
  </div>
  <div class="card shadow-lg">
    <div class="card-body login-card-body">
      <p class="login-box-msg font-weight-bold">تعيين كلمة مرور جديدة</p>

      <form action="{{ route('password.store') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $request->email) }}" placeholder="البريد الإلكتروني" required readonly>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
          @error('email')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="كلمة المرور الجديدة" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
          @error('password')
            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block text-white font-weight-bold">
               تحديث كلمة المرور <i class="fas fa-check-circle mr-1"></i>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>