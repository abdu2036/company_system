<aside class="main-sidebar sidebar-dark-primary elevation-4" style="direction: rtl; text-align: right;">
    <a href="{{ route('companies.index') }}" class="brand-link shadow-sm">
        <img src="{{ asset('assets/admin/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" 
             class="brand-image img-circle elevation-3" style="opacity: .8; float: right; margin-left: .8rem; margin-right: .5rem;">
        <span class="brand-text font-weight-light">نظام إدارة الشركات</span>
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

                <li class="nav-header text-left">التقارير والإحصائيات</li>

                <li class="nav-item">
                    <a href="{{ url('/reports') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line text-danger"></i>
                        <p>لوحة التقارير العامة</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>