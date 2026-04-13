@extends('layouts.admin') {{-- تأكد من اسم ملف الليوت عندك --}}

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">الدعم الفني</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                {!! $your_card_code !!} 
            </div>
        </div>
    </div>
</section>
@endsection