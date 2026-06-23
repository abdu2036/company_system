<aside class="main-sidebar sidebar-dark-primary elevation-4" style="direction: rtl; text-align: right;">
    <a href="{{ route('companies.index') }}" class="brand-link shadow-sm text-center py-3"
        style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-bottom: 1px solid #dee2e6;">

        <img src="{{ asset('assets/admin/dist/img/2026.png') }}" alt="A-Soft Icon"
            class="brand-image img-circle elevation-3 shadow-lg"
            style="opacity: 1; float: none; margin: 0 auto; display: block; max-height: 70px; border: 2px solid #fff; background-color: #fff; transition: transform 0.3s ease;">

        <span class="brand-text font-weight-bold d-block mt-2"
            style="font-family: 'Cairo', sans-serif; color: #1e4f9c; font-size: 1.2rem; line-height: 1.2;">
            Albuazi_<span class="text-success">soft</span>
        </span>

        <span class="brand-text d-block text-muted small mt-1" style="font-family: 'Cairo', sans-serif;">
            لإدارة الشركات و التراخيص
        </span>
    </a>

    <div class="sidebar">
        @auth
            <div class="user-panel mt-3 pb-3 mb-3 d-flex border-bottom" style="border-color: #4b545c !important;">
                <div class="image">
                    @php
                        $profilePhoto = auth()->user()->employee->profile_photo ?? null;
                        $hrmsUrl = "http://localhost/HRMS/storage/"; 
                    @endphp

                    <img src="{{ $profilePhoto ? $hrmsUrl . $profilePhoto : asset('assets/admin/dist/img/user2-160x160.jpg') }}"
                        class="img-circle elevation-2" alt="User Image"
                        style="width: 2.1rem; height: 2.1rem; object-fit: cover;"
                        onerror="this.src='{{ asset('assets/admin/dist/img/user2-160x160.jpg') }}';">
                </div>
                <div class="info">
                    <a href="#" class="d-block text-white" style="font-family: 'Cairo', sans-serif;">
                        {{ auth()->user()->employee->full_name ?? auth()->user()->name }}
                    </a>
                    <small class="text-warning">
                        <i class="fas fa-id-badge"></i> {{ auth()->user()->employee->jobTitle->name ?? 'موظف' }}
                    </small>
                </div>
            </div>
        @endauth

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                {{-- 🏢 1. قسم الشركات والتراخيص --}}
                @can('companies.view')
                    <li
                        class="nav-item has-treeview {{ request()->is('companies*', 'commercial-registers*', 'licenses*', 'chambers*', 'importers*', 'company-archives*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('companies*', 'commercial-registers*', 'licenses*', 'chambers*', 'importers*', 'company-archives*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tasks text-info"></i>
                            <p>
                                إدارة الشركات
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/companies') }}"
                                    class="nav-link {{ request()->is('companies') ? 'active' : '' }}">
                                    <i class="fas fa-city nav-icon"></i>
                                    <p>عرض الشركات</p>
                                </a>
                            </li>

                            @can('companies.create')
                                <li class="nav-item">
                                    <a href="{{ url('/companies/create') }}"
                                        class="nav-link {{ request()->is('companies/create') ? 'active' : '' }}">
                                        <i class="fas fa-plus-circle nav-icon"></i>
                                        <p>إضافة شركة جديدة</p>
                                    </a>
                                </li>
                            @endcan

                            <li class="nav-item">
                                <a href="{{ url('/commercial-registers') }}"
                                    class="nav-link {{ request()->is('commercial-registers*') ? 'active' : '' }}">
                                    <i class="fas fa-file-contract nav-icon"></i>
                                    <p>عرض السجل التجاري</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/licenses') }}"
                                    class="nav-link {{ request()->is('licenses*') ? 'active' : '' }}">
                                    <i class="fas fa-certificate nav-icon"></i>
                                    <p>عرض الترخيص</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/chambers') }}"
                                    class="nav-link {{ request()->is('chambers*') ? 'active' : '' }}">
                                    <i class="fas fa-store-alt nav-icon"></i>
                                    <p>عرض الغرفة التجارية</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/importers') }}"
                                    class="nav-link {{ request()->is('importers*') ? 'active' : '' }}">
                                    <i class="fas fa-ship nav-icon"></i>
                                    <p>عرض سجل المستوردين</p>
                                </a>
                            </li>
                            <li class="nav-item border-top mt-1 shadow-sm">
                                <a href="{{ url('/company-archives') }}"
                                    class="nav-link {{ request()->is('company-archives*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-archive text-warning"></i>
                                    <p>أرشفة الملفات</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- 💰 2. الحسابات النظام المالي (الإيرادات والمصروفات) --}}
                @if(auth()->user()->can('revenues.view') || auth()->user()->can('expenses.view') || auth()->user()->can('financial_reports.view'))
                    <li class="nav-item has-treeview {{ request()->is('finance*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('finance*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calculator text-success"></i>
                            <p>
                                الحسابات المالية
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('financial_reports.view')
                                <li class="nav-item">
                                    <a href="{{ url('/finance/companies') }}"
                                        class="nav-link {{ request()->is('finance/companies') ? 'active' : '' }}">
                                        <i class="fas fa-file-invoice-dollar nav-icon text-info"></i>
                                        <p>سجلات الشركات المالية</p>
                                    </a>
                                </li>
                            @endcan

                            @can('expenses.view')
                                <li class="nav-item">
                                    <a href="{{ route('expenses.index') }}"
                                        class="nav-link {{ request()->is('finance/expenses*') ? 'active' : '' }}">
                                        <i class="fas fa-minus-circle nav-icon text-danger"></i>
                                        <p>المصروفات التشغيلية</p>
                                    </a>
                                </li>
                            @endcan

                            @can('revenues.view')
                                <li class="nav-item">
                                    <a href="{{ route('revenues.index') }}"
                                        class="nav-link {{ request()->is('finance/revenues*') ? 'active' : '' }}">
                                        <i class="fas fa-plus-circle nav-icon text-success"></i>
                                        <p>الإيرادات التشغيلية</p>
                                    </a>
                                </li>
                            @endcan
                            @can('revenues.view')
                            <li class="nav-item">
                                <a href="{{ route('treasury.index') }}"
                                    class="nav-link {{ request()->routeIs('treasury.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-cash-register text-success"></i>
                                    <p>
                                        إدارة الخزينة والصناديق
                                        <span class="badge badge-info right">جديد</span>
                                    </p>
                                </a>
                            </li>
                             @endcan
                            {{-- حماية زر التقارير المالية والإحصائية بناءً على الصلاحية النقطية --}}
                            @can('financial_reports.view')
                                <li class="nav-item">
                                    <a href="{{ route('reports.financial') }}"
                                        class="nav-link {{ Route::is('reports.financial') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-chart-pie text-info"></i>
                                        <p class="font-arabic pr-2">
                                            التقارير والإحصائية
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- 📦 3. إدارة الأصول والعهد --}}
                @can('warehouses.view')
                    <li class="nav-item has-treeview {{ request()->is('assets*', 'property-list*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('assets*', 'property-list*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes text-warning"></i>
                            <p>
                                إدارة الأصول
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('assets.index') }}"
                                    class="nav-link {{ request()->is('assets') ? 'active' : '' }}">
                                    <i class="fas fa-list-ul nav-icon"></i>
                                    <p>الأصول النشطة</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('assets.damaged') }}"
                                    class="nav-link {{ request()->is('assets/damaged') ? 'active' : '' }}">
                                    <i class="fas fa-dumpster nav-icon"></i>
                                    <p>مخزن التوالف</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('assets.maintenance_logs') }}"
                                    class="nav-link {{ request()->is('assets/maintenance_logs*') ? 'active' : '' }}">
                                    <i class="fas fa-tools nav-icon"></i>
                                    <span>سجل الصيانة</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('assets.dashboard') }}"
                                    class="nav-link {{ request()->is('assets/dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-chart-pie nav-icon"></i>
                                    <p>التقارير والإحصائيات</p>
                                </a>
                            </li>

                            @can('warehouses.create')
                                <li class="nav-item border-top">
                                    <a href="{{ route('assets.create') }}"
                                        class="nav-link {{ request()->is('assets/create') ? 'active' : '' }}">
                                        <i class="fas fa-plus-circle nav-icon text-primary"></i>
                                        <p class="text-primary font-weight-bold">إضافة أصل جديد</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- 📊 4. التقارير العامة للمنظومة --}}
                @can('reports.view')
                    <li class="nav-header text-left">التقارير والإحصائيات</li>
                    <li class="nav-item">
                        <a href="{{ url('/reports') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line text-danger"></i>
                            <p>لوحة التقارير العامة</p>
                        </a>
                    </li>
                @endcan

                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link" data-toggle="modal" data-target="#contactModal">
                        <i class="fas fa-headset ml-3"></i> اتصل بنا
                    </a>
                </li>

                {{-- 🛡️ 5. إدارة الصلاحيات والأدوار (مغلق ومحمي للمخولين فقط) --}}
                @can('roles.view')
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}"
                            class="nav-link {{ request()->is('roles*', 'manage-roles*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>
                                إدارة الصلاحيات
                                <span class="badge badge-info right">HRMS</span>
                            </p>
                        </a>
                    </li>
                @endcan

            </ul>
        </nav>
    </div>
</aside>