<div class="content-wrapper">

    <!-- Content Header (العنوان + Breadcrumb) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <!-- Breadcrumb -->
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('companies.index') }}">الرئيسية</a>
                        </li>
                        <li class="breadcrumb-item active">
                            @yield('title')
                        </li>
                    </ol>
                </div>

                <!-- عنوان الصفحة -->
                <div class="col-sm-12">
                    <h1 class="m-0 text-dark">@yield('title')</h1>
                </div>

            </div>
        </div>
    </div>
    <!-- /.content-header -->


    <!-- ✅ المحتوى الرئيسي (المهم جداً) -->
    <section class="content">
        <div class="container-fluid">

            @yield('content')

        </div>
    </section>
    <!-- /.content -->

</div>