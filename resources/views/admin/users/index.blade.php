@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-5">
        <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-info fw-bold text-uppercase mb-0 letter-spacing-2">Quản lý người dùng (UC09)</h2>
    </div>

    <div class="profile-content shadow-lg">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-secondary">
                <thead>
                    <tr class="text-secondary border-bottom border-secondary">
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tham gia</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-secondary">#{{ $user->id }}</td>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge bg-info text-dark">Admin</span>
                            @else
                                <span class="badge bg-secondary">Thành viên</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-warning me-2" title="Chặn tài khoản">
                                <i class="bi bi-slash-circle"></i>
                            </button>
                            
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Nguyên có chắc chắn muốn xóa người dùng này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3"></i> XÓA
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection