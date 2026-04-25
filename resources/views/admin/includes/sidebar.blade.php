<aside class="main-sidebar sidebar-dark-primary elevation-4" style="direction: rtl; text-align: right;">
<a href="{{ route('companies.index') }}" 
   class="brand-link shadow-sm text-center py-3" 
   style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 1px solid #dee2e6;">
    
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
    
    <span class="brand-text font-weight-bold d-block mt-2" 
          style="font-family: 'Cairo', sans-serif; 
                 color: #1e4f9c; 
                 font-size: 1.2rem; /* تكبير حجم الخط قليلاً */
                 line-height: 1.2;">
        Albuazi_<span class="text-success">soft</span>
    </span>
    
    <span class="brand-text d-block text-muted small mt-1" style="font-family: 'Cairo', sans-serif;">
        لإدارة الشركات و التراخيص
    </span>
</a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" 
                data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item has-treeview {{ request()->is('companies*', 'commercial-registers*', 'licenses*', 'chambers*', 'importers*', 'company-archives*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('companies*', 'commercial-registers*', 'licenses*', 'chambers*', 'importers*', 'company-archives*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tasks text-info"></i>
                        <p>
                            إدارة الشركات
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/companies') }}" class="nav-link {{ request()->is('companies') ? 'active' : '' }}">
                                <i class="fas fa-city nav-icon"></i>
                                <p>عرض الشركات</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/companies/create') }}" class="nav-link {{ request()->is('companies/create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle nav-icon"></i>
                                <p>إضافة شركة جديدة</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/commercial-registers') }}" class="nav-link {{ request()->is('commercial-registers*') ? 'active' : '' }}">
                                <i class="fas fa-file-contract nav-icon"></i>
                                <p>عرض السجل التجاري</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/licenses') }}" class="nav-link {{ request()->is('licenses*') ? 'active' : '' }}">
                                <i class="fas fa-certificate nav-icon"></i>
                                <p>عرض الترخيص</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/chambers') }}" class="nav-link {{ request()->is('chambers*') ? 'active' : '' }}">
                                <i class="fas fa-store-alt nav-icon"></i>
                                <p>عرض الغرفة التجارية</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/importers') }}" class="nav-link {{ request()->is('importers*') ? 'active' : '' }}">
                                <i class="fas fa-ship nav-icon"></i>
                                <p>عرض سجل المستوردين</p>
                            </a>
                        </li>
                        
                        <li class="nav-item border-top mt-1 shadow-sm">
                            <a href="{{ url('/company-archives') }}" class="nav-link {{ request()->is('company-archives*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-archive text-warning"></i>
                                <p>أرشفة الملفات</p>
                            </a>
                        </li>
                    </ul>
                </li>

                                <li class="nav-item has-treeview {{ request()->is('finance*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('finance*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-calculator text-success"></i>
        <p>
            الحسابات المالية
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('/finance/companies') }}" class="nav-link {{ request()->is('finance/companies') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar nav-icon"></i>
                <p>سجلات الشركات المالية</p>
            </a>
        </li>
        
    </ul>
</li>

                <li class="nav-header text-left">التقارير والإحصائيات</li>

                <li class="nav-item">
                    <a href="{{ url('/reports') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line text-danger"></i>
                        <p>لوحة التقارير العامة</p>
                    </a>
                </li>

                   <li class="nav-item d-none d-sm-inline-block">
    <a href="#" class="nav-link" data-toggle="modal" data-target="#contactModal">
        <i class="fas fa-headset ml-3"></i> اتصل بنا
    </a>
</li>
            </ul>
        </nav>
    </div>
</aside>