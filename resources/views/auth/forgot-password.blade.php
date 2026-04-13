<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>نظام إدارة الشركات | استعادة كلمة المرور</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
  <style>
    body { font-family: 'Cairo', sans-serif; background-color: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
    .reset-card { max-width: 450px; width: 100%; background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-top: 5px solid #ffc107; text-align: center; }
    .form-control { border-radius: 10px; padding: 12px; }
    .btn-warning { border-radius: 10px; font-weight: 700; color: #212529; }
  </style>
</head>
<body>

<div class="reset-card">
    <div class="mb-4">
        <i class="fas fa-key fa-3x text-warning mb-3"></i>
        <h4 class="font-weight-bold">نسيت كلمة المرور؟</h4>
        <p class="text-muted small">أدخل بريدك الإلكتروني وسنرسل لك رابطاً لتعيين كلمة مرور جديدة.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success small mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group text-right">
            <label class="small font-weight-bold">البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control" placeholder="mail@example.com" required autofocus>
        </div>

        <button type="submit" class="btn btn-warning btn-block shadow-sm mb-3">إرسال رابط الاستعادة</button>
    </form>

    <a href="{{ route('login') }}" class="small text-muted font-weight-bold"><i class="fas fa-arrow-right ml-1"></i> العودة لتسجيل الدخول</a>
</div>

</body>
</html>