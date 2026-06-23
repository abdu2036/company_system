<nav class="main-header navbar navbar-expand navbar-light bg-white border-bottom shadow-sm custom-modern-navbar px-3">
    <ul class="navbar-nav d-flex align-items-center flex-row">
        <li class="nav-item">
            <a class="nav-link toggle-btn-modern mx-2" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block ms-3">
            <a href="{{ url('/companies') }}" class="nav-link fw-semibold text-secondary hover-primary transition-all">
                <i class="fas fa-home me-1 small"></i> الرئيسية
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto d-flex align-items-center flex-row">
        
        <li class="nav-item dropdown px-2">
            <a class="nav-link position-relative notification-trigger d-flex align-items-center justify-content-center" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-bell fs-5 text-dark"></i>
                {{-- عرض النقطة النابضة باللون الأحمر فقط إذا كان هناك تنبيهات فعلاً --}}
                @if(isset($expiredCount) && $expiredCount > 0)
                    <span class="position-absolute top-2 start-60 translate-middle badge rounded-pill bg-danger p-1 border border-white pulse-animation">
                        <span class="visually-hidden">تنبيهات</span>
                    </span>
                @endif
            </a>
            
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-start border-0 shadow-lg custom-notification-dropdown animate slideIn text-right">
                <div class="dropdown-header-custom p-3 d-flex justify-content-between align-items-center bg-light rounded-top">
                    <span class="fw-bold text-dark fs-6">تنبيهات انتهاء الصلاحية</span>
                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-2 py-1">{{ $expiredCount ?? 0 }} شركة</span>
                </div>
                
                <div class="dropdown-divider m-0"></div>
                
                <div class="notification-list-scroll">
                    {{-- عرض الشركات المنتهية --}}
                    @if(isset($expiredCompanies) && $expiredCompanies->count() > 0)
                        @foreach($expiredCompanies as $company)
                            <a href="{{ route('companies.show', $company->id) }}" class="dropdown-item p-3 d-flex align-items-center justify-content-between border-bottom-light">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fas fa-exclamation-triangle small"></i>
                                    </div>
                                    <div class="text-right">
                                        <span class="fw-bold d-block text-dark small">شركة: {{ $company->name }}</span>
                                        <span class="text-muted small d-block header-subtext">انتهت صلاحية الترخيص الخاص بها</span>
                                    </div>
                                </div>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small-status">منتهي</span>
                            </a>
                        @endforeach
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="far fa-bell-slash fa-2x mb-2 text-black-50 d-block"></i>
                            <p class="mb-0 small">لا توجد تراخيص منتهية حالياً</p>
                        </div>
                    @endif
                </div>
                
                <div class="dropdown-divider m-0"></div>
                <a href="#" class="dropdown-item text-center text-primary fw-semibold py-2 rounded-bottom small custom-footer-btn">
                    عرض تفاصيل جميع التراخيص <i class="fas fa-arrow-left ms-1 small"></i>
                </a>
            </div>
        </li>

        <li class="nav-item d-none d-sm-block px-2">
            <span class="text-muted opacity-25">|</span>
        </li>

        <li class="nav-item px-1">
            <a class="nav-link logout-btn-modern text-danger d-flex align-items-center px-3 py-2 rounded-pill" href="#" onclick="event.preventDefault(); confirmLogout();" title="تسجيل الخروج">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-none d-md-inline ms-2 fw-bold small">خروج</span>
            </a>
        </li>
    </ul>
</nav>

{{-- فورم تسجيل الخروج المخفي --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
    .custom-modern-navbar {
        min-height: 65px;
        background-color: #ffffff !important;
        border-bottom: 1px solid #edf2f7 !important;
    }
    
    .toggle-btn-modern {
        color: #4b5563 !important;
        padding: 10px !important;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .toggle-btn-modern:hover {
        background-color: #f3f4f6;
        color: #2563eb !important;
    }

    .transition-all {
        transition: all 0.2s ease;
    }
    .hover-primary:hover {
        color: #2563eb !important;
    }
    
    .logout-btn-modern {
        background-color: rgba(239, 68, 68, 0.06);
        border: 1px solid rgba(239, 68, 68, 0.1);
        transition: all 0.2s ease;
        line-height: 1;
    }
    .logout-btn-modern:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }

    .custom-notification-dropdown {
        width: 340px !important;
        border-radius: 12px !important;
        overflow: hidden;
        padding: 0;
        margin-top: 12px !important;
    }
    .notification-list-scroll {
        max-height: 280px;
        overflow-y: auto;
    }
    .border-bottom-light {
        border-bottom: 1px solid #f3f4f6;
    }
    .border-bottom-light:hover {
        background-color: #f9fafb;
    }
    .header-subtext {
        font-size: 11px;
        color: #9ca3af !important;
    }
    .small-status {
        font-size: 11px;
    }
    .custom-footer-btn:hover {
        background-color: #f0f7ff !important;
    }

    /* أنيميشن خفيف ينبض لجذب الانتباه لوجود تنبيهات منتهية */
    .pulse-animation {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 5px rgba(239, 68, 68, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }

    @media (min-width: 768px) {
        .animate {
            animation-duration: 0.2s;
            animation-fill-mode: both;
        }
        .slideIn {
            animation-name: slideIn;
        }
    }
    @keyframes slideIn {
        0% {
            transform: translateY(10px);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>