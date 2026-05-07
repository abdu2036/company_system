@extends('layouts.admin')
@section('title', 'إدارة الأدوار والصلاحيات المتقدمة')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-shield-alt text-primary mr-2"></i> منظومة التحكم بالصلاحيات
        </h1>
        <button type="button" class="btn btn-primary btn-sm shadow-sm px-4" data-toggle="modal" data-target="#addRoleModal">
            <i class="fas fa-plus-circle fa-sm text-white-50 mr-1"></i> إضافة دور جديد
        </button>
    </div>

    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <div class="row">
        <div class="col-xl-5 col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3"><h6 class="m-0 font-weight-bold text-primary">تخصيص الأدوار</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light sticky-top">
                                <tr><th>الموظف</th><th>الدور المسند</th></tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle mr-2 bg-primary text-white">{{ Str::limit($user->name, 1, '') }}</div>
                                            <div class="small font-weight-bold">{{ $user->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('roles.update', $user->id) }}" method="POST" id="form-user-{{ $user->id }}">
                                            @csrf
                                            <select name="role" class="form-control form-control-sm select-role" 
                                                    onchange="if(confirm('تغيير دور الموظف؟')) { this.form.submit(); } else { this.value='{{ $user->getRoleNames()->first() }}'; }">
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6">
            <div class="card shadow-sm border-0 mb-4 border-left-success">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">مصفوفة الصلاحيات (تحديث فوري)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light text-center">
                                <tr><th class="text-right">نوع الصلاحية</th>@foreach($roles as $role)<th>{{ $role->name }}</th>@endforeach</tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    'edit assets' => ['name' => 'تعديل الأصول', 'icon' => 'fa-edit', 'color' => 'text-primary'],
                                    'delete assets' => ['name' => 'حذف الأصول', 'icon' => 'fa-trash-alt', 'color' => 'text-danger'],
                                    'manage maintenance' => ['name' => 'الصيانة', 'icon' => 'fa-tools', 'color' => 'text-warning'],
                                    'view reports' => ['name' => 'التقارير', 'icon' => 'fa-file-pdf', 'color' => 'text-info']
                                ] as $permKey => $permData)
                                <tr>
                                    <td class="text-right py-3">
                                        <i class="fas {{ $permData['icon'] }} {{ $permData['color'] }} mr-2"></i> {{ $permData['name'] }}
                                    </td>
                                    @foreach($roles as $role)
                                    <td class="text-center align-middle">
                                        <div class="custom-control custom-switch custom-switch-lg">
                                            <input type="checkbox" class="custom-control-input permission-switch" 
                                                   id="sw-{{ $role->id }}-{{ $loop->parent->index }}"
                                                   data-role="{{ $role->id }}" data-permission="{{ $permKey }}"
                                                   {{ $role->hasPermissionTo($permKey) ? 'checked' : '' }}>
                                            <label class="custom-control-label cursor-pointer" for="sw-{{ $role->id }}-{{ $loop->parent->index }}"></label>
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle { height: 35px; width: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; }
    .custom-switch-lg .custom-control-label::before { height: 1.5rem; width: 2.75rem; border-radius: 1rem; }
    .custom-switch-lg .custom-control-label::after { width: calc(1.5rem - 4px); height: calc(1.5rem - 4px); }
    .custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after { transform: translateX(1.25rem); }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // تهيئة Axios للتعامل مع CSRF
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // نظام التنبيهات (Toast)
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} shadow-sm`;
        toast.style.marginBottom = '10px';
        toast.innerText = message;
        const container = document.getElementById('toast-container');
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    document.querySelectorAll('.permission-switch').forEach(item => {
        item.addEventListener('change', function() {
            const row = this.closest('tr');
            row.style.opacity = '0.5';

            axios.post("{{ route('roles.update_permission') }}", {
                role_id: this.dataset.role,
                permission: this.dataset.permission,
                status: this.checked
            })
            .then(res => {
                row.style.opacity = '1';
                showToast('تم تحديث الصلاحية بنجاح');
            })
            .catch(err => {
                row.style.opacity = '1';
                this.checked = !this.checked; // عكس الحركة
                showToast('حدث خطأ، يرجى المحاولة لاحقاً', 'danger');
            });
        });
    });
</script>
@endsection