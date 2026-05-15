@extends('layouts.app')

@section('title', 'Quản lý Tài khoản')
@section('page_title', '')

@section('content')
<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h3 class="mb-0 fw-bold" style="color: #2d3748;">
                <i class="fas fa-users-cog me-2 text-primary"></i> Quản lý Tài khoản
            </h3>
            <p class="text-muted mb-0 small">Quản lý toàn bộ tài khoản người dùng trong hệ thống</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-user-plus me-1"></i> Tạo tài khoản mới
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('temp_password_info'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm border-start border-warning border-4" role="alert">
            <i class="fas fa-key me-2"></i> {!! session('temp_password_info') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search Bar --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="search" class="form-control border-start-0"
                               placeholder="Tìm theo tên, username, email, SĐT...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="roleFilter" class="form-select">
                        <option value="">-- Tất cả vai trò --</option>
                        @foreach(\App\Models\Role::where('status', 1)->get() as $role)
                            <option value="{{ $role->role_code }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="resetSearch" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>Tài khoản</th>
                            <th>Thông tin liên hệ</th>
                            <th class="text-center">Vai trò</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="userTable">
                        @forelse($users as $index => $user)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $user->avatar_url ? asset($user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=667eea&color=fff&size=40' }}"
                                         class="rounded-circle border" width="40" height="40" style="object-fit:cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->full_name }}</div>
                                        <small class="text-muted"><i class="fas fa-at me-1"></i>{{ $user->username }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    @if($user->email)
                                        <div><i class="fas fa-envelope text-muted me-1"></i>{{ $user->email }}</div>
                                    @endif
                                    @if($user->phone_number)
                                        <div><i class="fas fa-phone text-muted me-1"></i>{{ $user->phone_number }}</div>
                                    @endif
                                    @if(!$user->email && !$user->phone_number)
                                        <span class="text-muted">Chưa cập nhật</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @forelse($user->roles as $role)
                                    @php
                                        $roleColors = [
                                            'ADMIN' => 'danger',
                                            'DOCTOR' => 'primary',
                                            'RECEPTIONIST' => 'info',
                                            'CASHIER' => 'success',
                                            'PATIENT' => 'secondary',
                                        ];
                                        $color = $roleColors[$role->role_code] ?? 'dark';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $role->role_name }}</span>
                                @empty
                                    <span class="badge bg-light text-dark border">Chưa cấp</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                @if($user->status == 1)
                                    <span class="badge rounded-pill bg-success">
                                        <i class="fas fa-circle me-1" style="font-size:0.6rem;"></i>Hoạt động
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger">
                                        <i class="fas fa-circle me-1" style="font-size:0.6rem;"></i>Bị khóa
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-info btn-sm" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-warning btn-sm" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Xóa tài khoản {{ $user->username }}? Hành động này không thể hoàn tác!')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled title="Đây là tài khoản của bạn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fs-1 mb-3 d-block text-black-50"></i>
                                Chưa có tài khoản nào. <a href="{{ route('users.create') }}">Tạo tài khoản đầu tiên</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Tổng: {{ $users->total() }} tài khoản</small>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const roleColors = {
        'ADMIN': 'danger', 'DOCTOR': 'primary', 'RECEPTIONIST': 'info',
        'CASHIER': 'success', 'PATIENT': 'secondary'
    };

    function doSearch() {
        const keyword = $('#search').val();
        const role = $('#roleFilter').val();

        $.ajax({
            url: "{{ route('users.ajax.search') }}",
            type: "GET",
            data: { keyword, role },
            success: function (data) {
                let html = '';
                if (data.length === 0) {
                    html = `<tr><td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-search fs-1 mb-3 d-block text-black-50"></i>
                        Không tìm thấy tài khoản nào phù hợp.
                    </td></tr>`;
                } else {
                    data.forEach(function (user, index) {
                        const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.full_name)}&background=667eea&color=fff&size=40`;
                        let rolesHtml = user.roles.length > 0
                            ? user.roles.map(r => `<span class="badge bg-${roleColors[r.role_code] || 'dark'}">${r.role_name}</span>`).join(' ')
                            : '<span class="badge bg-light text-dark border">Chưa cấp</span>';

                        let statusHtml = user.status == 1
                            ? '<span class="badge rounded-pill bg-success"><i class="fas fa-circle me-1" style="font-size:0.6rem;"></i>Hoạt động</span>'
                            : '<span class="badge rounded-pill bg-danger"><i class="fas fa-circle me-1" style="font-size:0.6rem;"></i>Bị khóa</span>';

                        let contactHtml = '';
                        if (user.email) contactHtml += `<div><i class="fas fa-envelope text-muted me-1"></i>${user.email}</div>`;
                        if (user.phone_number) contactHtml += `<div><i class="fas fa-phone text-muted me-1"></i>${user.phone_number}</div>`;
                        if (!user.email && !user.phone_number) contactHtml = '<span class="text-muted">Chưa cập nhật</span>';

                        const currentUserId = {{ auth()->id() }};
                        const deleteBtn = user.id !== currentUserId
                            ? `<form action="/users/${user.id}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài khoản ${user.username}?')">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-outline-danger btn-sm" title="Xóa"><i class="fas fa-trash"></i></button>
                               </form>`
                            : `<button class="btn btn-outline-secondary btn-sm" disabled title="Đây là tài khoản của bạn"><i class="fas fa-trash"></i></button>`;

                        html += `<tr>
                            <td class="ps-4 text-muted small">${index + 1}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="${avatar}" class="rounded-circle border" width="40" height="40" style="object-fit:cover;">
                                    <div>
                                        <div class="fw-bold text-dark">${user.full_name || ''}</div>
                                        <small class="text-muted"><i class="fas fa-at me-1"></i>${user.username}</small>
                                    </div>
                                </div>
                            </td>
                            <td><div class="small">${contactHtml}</div></td>
                            <td class="text-center">${rolesHtml}</td>
                            <td class="text-center">${statusHtml}</td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <a href="/users/${user.id}" class="btn btn-outline-info btn-sm" title="Xem"><i class="fas fa-eye"></i></a>
                                    <a href="/users/${user.id}/edit" class="btn btn-outline-warning btn-sm" title="Sửa"><i class="fas fa-edit"></i></a>
                                    ${deleteBtn}
                                </div>
                            </td>
                        </tr>`;
                    });
                }
                $('#userTable').html(html);
            }
        });
    }

    $('#search').on('keyup', doSearch);
    $('#roleFilter').on('change', doSearch);
    $('#resetSearch').on('click', function () {
        $('#search').val('');
        $('#roleFilter').val('');
        location.reload();
    });
});
</script>
@endpush
