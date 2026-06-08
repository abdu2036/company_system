@extends('layouts.admin')

@section('title', 'تعديل الدور والصلاحيات')

@section('content_header')
    <div class="container-fluid font-arabic">
        <div class="row mb-2">
            <div class="col-sm-6 text-right">
                <h1 class="m-0 text-dark">
                    تعديل الدور: 
                    <span class="text-primary">
                        {{ ($role->name == 'admin' || $role->name == 'super-admin') ? 'المدير العام 🛡️' : (Lang::has('permissions.' . $role->name) ? __('permissions.' . $role->name) : $role->name) }}
                    </span>
                </h1>
            </div>
            <div class="col-sm-6 text-left">
                <ol class="breadcrumb float-sm-left bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">إدارة الأدوار</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row font-arabic d-flex justify-content-center">
        <div class="col-md-11 col-sm-12">
            <div class="card card-outline card-primary shadow-sm text-right">
                <div class="card-header border-0">
                    <h3 class="card-title float-right font-weight-bold mb-0">
                        <i class="fas fa-edit ml-1 text-primary"></i> تحديث بيانات الدور والصلاحيات
                    </h3>
                </div>

                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        {{-- حقل اسم الدور الثابت --}}
                        <div class="form-group mb-4">
                            <label for="role_name" class="font-weight-bold text-secondary">
                                اسم الدور <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="role_name" value="{{ $role->name }}" 
                                   class="form-control text-left font-weight-bold bg-light" 
                                   readonly required style="letter-spacing: 0.5px;">
                            <small class="text-muted d-block mt-1">اسم المفتاح البرمجي للدور محمي ولا يمكن تعديله لضمان استقرار النظام.</small>
                        </div>

                        <hr class="my-4">

                        {{-- قسم تعديل الصلاحيات الممنوحة مقسم إلى مجموعات --}}
                        <h5 class="text-primary font-weight-bold mb-4">
                            <i class="fas fa-key ml-1 text-warning"></i> تعديل الصلاحيات الممنوحة مرتبة حسب القسم:
                        </h5>

                        <div class="row">
                            @foreach($permissionsGrouped as $groupName => $permissions)
                                <div class="col-md-6 mb-4">
                                    <div class="card card-outline card-secondary shadow-sm h-100" style="border-radius: 8px; border-top: 3px solid #6c757d;">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="card-title font-weight-bold text-dark mb-0" style="font-size: 0.95rem;">
                                                @switch($groupName)
                                                    @case('employees') 👥 إدارة شؤون الموظفين @break
                                                    @case('companies') 🏢 إدارة الشركات والتراخيص @break
                                                    @case('branches') 📍 إدارة الفروع @break
                                                    @case('warehouses') 📦 إدارة الأصول والمخازن @break
                                                    @case('salaries') 💵 منظومة الرواتب والمالية @break
                                                    @case('revenues') 📈 الحسابات - الإيرادات @break
                                                    @case('expenses') 📉 الحسابات - المصروفات @break
                                                    @case('reports') 📊 لوحة التقارير العامة @break
                                                    @case('financial_reports') 💰 التقارير المالية والإحصائية @break
                                                    @case('users') 👤 إدارة مستخدمي المنظومة @break
                                                    @case('roles') 🔒 إدارة الأدوار والوظائف @break
                                                    @case('permissions') 🔑 التحكم بالصلاحيات النقطية @break
                                                    @case('settings') ⚙️ الإعدادات العامة للنظام @break
                                                    @default 📂 قسم: {{ ucfirst($groupName) }}
                                                @endswitch
                                            </h6>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="row">
                                                @foreach($permissions as $permission)
                                                    @php
                                                        // استخراج الأكشن الفرعي للترجمة: "companies.create" تعطينا "create"
                                                        $parts = explode('.', $permission->name);
                                                        $action = isset($parts[1]) ? $parts[1] : null;
                                                    @endphp
                                                    <div class="col-sm-6 my-2">
                                                        <div class="custom-control custom-checkbox d-flex align-items-center">
                                                            {{-- لاحظ هنا قمنا بإرجاع value ليكون اسم الصلاحية النصي ليتوافق مع الـ Controller والـ Spatie --}}
                                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                                   id="perm-{{ $permission->id }}" class="custom-control-input style-checkbox"
                                                                   {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                            
                                                            <label class="custom-control-label font-weight-normal pr-4 select-none mb-0 text-dark" for="perm-{{ $permission->id }}" style="cursor: pointer; user-select: none; font-size: 13.5px;">
                                                                @if($action && Lang::has("permissions.{$groupName}.{$action}"))
                                                                    {{ __("permissions.{$groupName}.{$action}") }}
                                                                @elseif(Lang::has("permissions.{$groupName}.manage"))
                                                                    {{ __("permissions.{$groupName}.manage") }}
                                                                @else
                                                                    {{ $permission->name }}
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- أزرار التحكم بالنموذج --}}
                    <div class="card-footer bg-white border-0 d-flex justify-content-between flex-row-reverse pb-4">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">
                            <i class="fas fa-sync-alt ml-1"></i> تحديث البيانات
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">
                            إلغاء التعديل
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        .font-arabic { font-family: 'Cairo', sans-serif !important; text-align: right !important; }
        
        /* فيكس متكامل لمحاذاة الـ Checkbox الخاص بـ AdminLTE إلى اليمين بطريقة صحيحة */
        .custom-control {
            padding-left: 0 !important;
            padding-right: 1.5rem !important;
        }
        .custom-control-label::before, .custom-control-label::after {
            right: 0 !important;
            left: auto !important;
        }
        .custom-control-input {
            right: 0 !important;
            left: auto !important;
        }
        .custom-control:hover .custom-control-label {
            color: #0284c7 !important;
        }
        .select-none {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
@stop