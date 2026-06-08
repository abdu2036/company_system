@extends('layouts.admin')

@section('title', 'إدارة الأدوار والصلاحيات')

@section('content_header')
    <div class="container-fluid font-arabic">
        <div class="row mb-2">
            <div class="col-sm-6 text-right">
                <h1 class="m-0 text-dark">إدارة الأدوار وبنية الصلاحيات</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    {{-- الرسائل التنبيهية للعمليات --}}
    @if(session('success'))
        <div class="alert alert-success text-right shadow-sm alert-dismissible fade show font-arabic" role="alert">
            <i class="fas fa-check-circle ml-2"></i> {{ session('success') }}
            <button type="button" class="close ml-0 mr-auto" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-right shadow-sm alert-dismissible fade show font-arabic" role="alert">
            <i class="fas fa-exclamation-circle ml-2"></i> {{ session('error') }}
            <button type="button" class="close ml-0 mr-auto" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row font-arabic">
        {{-- قسم إدارة الأدوار والصلاحيات (الجانب الأيمن - مساحة أكبر) --}}
        <div class="col-lg-8 col-md-12">
            <div class="card card-outline card-primary shadow-sm text-right">
                <div class="card-header border-0">
                    <h3 class="card-title float-right">
                        <i class="fas fa-shield-alt ml-1 text-primary"></i> قائمة الأدوار والمسميات الوظيفية المسجلة
                    </h3>
                    <div class="card-tools float-left">
                        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                            <i class="fas fa-plus ml-1"></i> إضافة دور جديد
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-navy">
                            <tr>
                                <th style="width: 50px" class="text-center">#</th>
                                <th>اسم الدور الوظيفي</th>
                                <th>الصلاحيات الممنوحة</th>
                                <th class="text-center" style="width: 120px">العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td class="text-center font-weight-bold text-secondary">{{ sprintf('%02d', $loop->iteration) }}</td>
                                    <td>
                                        <span class="badge badge-info p-2" style="font-size: 12px; font-weight: 600;">
                                            @if($role->name == 'admin' || $role->name == 'super-admin')
                                                المدير العام 🛡️
                                            @elseif(Lang::has('permissions.' . $role->name))
                                                {{ __('permissions.' . $role->name) }}
                                            @else
                                                {{ $role->name }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @forelse($role->permissions as $permission)
                                            <span class="badge badge-light border ml-1 mb-1 p-1" style="font-size: 11px;">
                                                <i class="fas fa-tag text-primary ml-1"></i> 
                                                @if(Lang::has('permissions.' . $permission->name))
                                                    {{ __('permissions.' . $permission->name) }}
                                                @elseif(Lang::has('permissions.permissions.' . $permission->name))
                                                    {{ __('permissions.permissions.' . $permission->name) }}
                                                @else
                                                    {{-- حل برمي مخصص للمسافات والنقاط --}}
                                                    {{ __('permissions.' . str_replace(' ', '_', $permission->name)) != 'permissions.' . str_replace(' ', '_', $permission->name) ? __('permissions.' . str_replace(' ', '_', $permission->name)) : $permission->name }}
                                                @endif
                                            </span>
                                        @empty
                                            <small class="text-muted">لا توجد صلاحيات مخصصة</small>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-xs btn-outline-info mx-1" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $role->id }}, '{{ $role->name }}')" class="btn btn-xs btn-outline-danger mx-1" title="حذف">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- قسم الموظفين المبسط (الجانب الأيسر) --}}
        <div class="col-lg-4 col-md-12">
            <div class="card card-outline card-success shadow-sm text-right">
                <div class="card-header border-0">
                    <h3 class="card-title float-right text-success font-weight-bold">
                        <i class="fas fa-users ml-1"></i> موظفو نظام HRMS
                    </h3>
                </div>
                
                <div class="card-body p-0" style="max-height: 520px; overflow-y: auto;">
                    <ul class="list-group list-group-flush pr-0">
                        @forelse ($users as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3 table-hover">
                                <div class="text-right">
                                    <h6 class="mb-0 font-weight-bold text-dark">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                    <div class="mt-1">
                                        @if($user->current_role)
                                            <span class="badge badge-primary p-1" style="font-size: 10px;">
                                                <i class="fas fa-user-shield ml-1"></i> 
                                                {{ ($user->current_role == 'admin' || $user->current_role == 'super-admin') ? 'المدير العام' : (Lang::has('permissions.' . $user->current_role) ? __('permissions.' . $user->current_role) : $user->current_role) }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary p-1 text-muted" style="font-size: 10px;">
                                                <i class="fas fa-user-times ml-1"></i> بدون دور حالياً
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" 
                                        class="btn btn-sm btn-success shadow-sm" 
                                        data-toggle="modal" 
                                        data-target="#assignRoleModal"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-current-role="{{ $user->current_role }}"
                                        title="إسناد / تغيير الدور">
                                    <i class="fas fa-user-cog"></i> تعيين
                                </button>
                            </li>
                        @empty
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-info-circle ml-1"></i> لا توجد بيانات موظفين قادمة.
                            </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- النافذة المنبثقة (Modal) --}}
    <div class="modal fade font-arabic" id="assignRoleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="assignRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white border-0 d-flex flex-row-reverse justify-content-between">
                    <h5 class="modal-title font-weight-bold" id="assignRoleModalLabel">
                        <i class="fas fa-user-shield ml-2"></i> تعديل الدور الوظيفي للموظف
                    </h5>
                    <button type="button" class="close text-white m-0 p-0 header-close-btn" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="assignRoleForm" method="POST" action="">
                    @csrf
                    <div class="modal-body text-right p-4">
                        <div class="form-group mb-3 bg-light p-3 rounded border">
                            <label class="text-muted mb-1 d-block" style="font-size: 13px;">اسم الموظف المستهدف</label>
                            <h5 id="modalUserName" class="font-weight-bold text-dark mb-0">--</h5>
                        </div>

                        <div class="form-group">
                            <label for="modalRoleSelect" class="font-weight-bold text-secondary mb-2">اختر المسمى الوظيفي الجديد داخل الشركات:</label>
                            <select name="role" id="modalRoleSelect" class="form-control text-right shadow-sm" required style="height: 42px;">
                                <option value="">-- اختر المسمى الوظيفي --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">
                                        {{ ($role->name == 'admin' || $role->name == 'super-admin') ? 'المدير العام 🛡️' : (Lang::has('permissions.' . $role->name) ? __('permissions.' . $role->name) : $role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 d-flex justify-content-start">
                        <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm">
                            <i class="fas fa-save ml-1"></i> حفظ وتحديث الربط
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-3" data-dismiss="modal">إلغاء الأمر</button>
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
        .table th, .table td { vertical-align: middle !important; }
        .bg-navy { background-color: #001f3f !important; color: #fff; }
        .header-close-btn { font-size: 1.5rem; line-height: 1; }
        .card-body::-webkit-scrollbar { width: 5px; }
        .card-body::-webkit-scrollbar-track { background: #f1f1f1; }
        .card-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#assignRoleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var userId = button.data('user-id'); 
            var userName = button.data('user-name'); 
            var currentRole = button.data('current-role'); 

            var modal = $(this);
            modal.find('#modalUserName').text(userName); 
            modal.find('#modalRoleSelect').val(currentRole); 

            var actionUrl = "{{ route('roles.update_employee', ':id') }}";
            actionUrl = actionUrl.replace(':id', userId);
            modal.find('#assignRoleForm').attr('action', actionUrl);
        });

        function confirmDelete(id, roleName) {
            if (roleName === 'admin' || roleName === 'super-admin') {
                Swal.fire({
                    icon: 'error',
                    title: 'محظور!',
                    text: 'لا يمكن حذف دور المدير العام لحماية استقرار النظام الأساسي.',
                    confirmButtonText: 'موافق'
                });
                return;
            }

            Swal.fire({
                title: 'هل أنت متأكد من الحذف؟',
                text: "سيتم حذف هذا الدور بالكامل، والموظفون المرتبطون به سيفقدون صلاحياتهم!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، قم بالحذف!',
                cancelButtonText: 'إلغاء',
                direction: 'rtl'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@stop