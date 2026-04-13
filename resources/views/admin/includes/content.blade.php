 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('companies.index') }}">الرئيسية</a></li>
              <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
            
          </div><!-- /.col -->
          <div class="col-sm-12">
            <h1 class="m-0 text-dark">@yield('title')</h1>
            <br>
            @yield('content')
          </div><!-- /.col -->

        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
 
    <!-- /.content -->
  </div>