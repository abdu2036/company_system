@extends('adminlte::page')

@section('title', 'السجلات التجارية')

@section('content_header')
    <h1>قائمة السجلات التجارية</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>رقم السجل</th>
                        <th>اسم الشركة</th>
                        <th>تاريخ الانتهاء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registers as $register)
                        <tr>
                            <td>{{ $register->cr_number }}</td>
                            <td>{{ $register->company->name ?? 'غير محدد' }}</td>
                            <td>{{ $register->expiry_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop