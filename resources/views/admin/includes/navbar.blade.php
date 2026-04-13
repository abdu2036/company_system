<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{ url('/companies') }}" class="nav-link">الرئيسية</a>
      </li>
 
    </ul>

    <ul class="navbar-nav ml-auto">
      
      

      <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        {{-- عرض الرقم فقط إذا كان هناك تنبيهات فعلاً --}}
        @if(isset($expiredCount) && $expiredCount > 0)
            <span class="badge badge-warning navbar-badge">{{ $expiredCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right text-right">
        <span class="dropdown-header font-weight-bold">تنبيهات انتهاء الصلاحية ({{ $expiredCount ?? 0 }})</span>
        <div class="dropdown-divider"></div>
        
        {{-- عرض الشركات المنتهية --}}
        @if(isset($expiredCompanies) && $expiredCompanies->count() > 0)
            @foreach($expiredCompanies as $company)
                <a href="{{ route('companies.show', $company->id) }}" class="dropdown-item">
                    <i class="fas fa-exclamation-triangle mr-2 text-danger"></i> 
                    شركة: {{ $company->name }}
                    <span class="float-left text-muted text-sm">منتهي</span>
                </a>
                <div class="dropdown-divider"></div>
            @endforeach
        @else
            <a href="#" class="dropdown-item text-center">لا توجد تنبيهات حالياً</a>
        @endif
        
        <a href="#" class="dropdown-item dropdown-footer">عرض تفاصيل جميع التراخيص</a>
    </div>
</li>

      <li class="nav-item">
        <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); confirmLogout();" title="خروج">
          <i class="fas fa-sign-out-alt"></i>
          <span class="d-none d-md-inline ml-1 font-weight-bold">خروج</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
</nav>

{{-- فورم تسجيل الخروج (يجب وضعه خارج وسم الـ nav لضمان عدم تأثره بالتنسيق) --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>