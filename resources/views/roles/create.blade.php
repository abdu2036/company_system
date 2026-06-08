@extends('layouts.admin')

@section('title', 'إضافة دور جديد')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-right">
                <h1 class="m-0 text-dark">
                    <i class="fas fa-plus-circle ml-2 text-primary"></i> إضافة دور جديد
                </h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-right ml-1"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-outline card-primary shadow-lg border-0">
                    <div class="card-header bg-navy py-3">
                        <h3 class="card-title float-right font-weight-bold text-white">
                            <i class="fas fa-shield-alt ml-2"></i> بيانات الدور الجديد
                        </h3>
                    </div>

                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        <div class="card-body text-right">
                            
                            {{-- اسم الدور --}}
                            <div class="form-group mb-4">
                                <label for="name" class="text-navy font-weight-bold">
                                    اسم الدور (باللغة الإنجليزية) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" 
                                       class="form-control form-control-lg shadow-sm @error('name') is-invalid @enderror" 
                                       placeholder="مثلاً: hr-manager" value="{{ old('name') }}" required style="text-align: left;">
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle ml-1"></i> يفضل كتابة الاسم بالإنجليزية لضمان استقرار النظام (مثل: editor, manager).
                                </small>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <hr class="my-4">

                            {{-- تحديد الصلاحيات --}}
                            <div class="form-group">
                                <label class="text-navy font-weight-bold mb-3 d-block">
                                    <i class="fas fa-key ml-1 text-primary"></i> تحديد الصلاحيات لهذا الدور:
                                </label>

                                <div class="row bg-light p-4 rounded shadow-inner border mx-0">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="custom-checkbox-card p-3 bg-white rounded border hover-shadow transition d-flex align-items-center">
                                                <input type="checkbox" name="permissions[]" 
                                                       value="{{ $permission->name }}" 
                                                       id="perm_{{ $permission->id }}" 
                                                       class="style-checkbox ml-3">
                                                
                                                <label class="checkbox-card-label pr-1 cursor-pointer text-gray-700 m-0" for="perm_{{ $permission->id }}">
                                                    @if(Lang::has('permissions.' . $permission->name))
                                                        {{ __('permissions.' . $permission->name) }}
                                                    @else
                                                        {{-- حل مشكلة المسافات في قاعدة البيانات والبحث عنها بشرطة سفلية تلقائياً --}}
                                                        @php 
                                                            $cleanKey = str_replace(' ', '_', $permission->name);
                                                        @endphp
                                                        {{ Lang::has('permissions.' . $cleanKey) ? __('permissions.' . $cleanKey) : $permission->name }}
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('permissions')
                                    <div class="text-danger mt-2 small"><strong>{{ $message }}</strong></div>
                                @enderror
                            </div>
                        </div>

                        {{-- أزرار التحكم --}}
                        <div class="card-footer bg-white border-top py-4">
                            <div class="d-flex justify-content-between align-items-center flex-row-reverse">
                                <button type="submit" class="btn btn-primary px-5 shadow font-weight-bold py-2">
                                    <i class="fas fa-save ml-2"></i> حفظ الدور الجديد
                                </button>
                                <button type="reset" class="btn btn-link text-muted font-weight-bold">
                                    <i class="fas fa-undo ml-1"></i> إعادة ضبط الحقول
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        body { font-family: 'Cairo', sans-serif !important; }
        .bg-navy { background-color: #001f3f !important; }
        .text-navy { color: #001f3f !important; }
        .card-title { float: right !important; }
        
        /* تأثيرات وتحسينات الصلاحيات لتناسب الـ RTL تماماً */
        .hover-shadow:hover { 
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            border-color: #007bff !important;
            background-color: #f8fafc !important;
        }
        .custom-checkbox-card {
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            user-select: none;
        }
        .style-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #007bff; /* يلون المربع باللون الأزرق عند التحديد */
        }
        .checkbox-card-label {
            width: 100%;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            color: #334155;
        }
        .custom-checkbox-card:hover .checkbox-card-label {
            color: #007bff;
        }
    </style>
@stop