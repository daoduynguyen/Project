@extends('layouts.admin')

@section('admin_content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div class="d-flex align-items-center">
                <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
                <h2 class="text-info fw-bold text-uppercase mb-0 letter-spacing-2">Quản lý kho vé VR</h2>
            </div>
            {{-- Link thêm mới --}}
            <a href="{{ route('admin.tickets.create') }}" class="btn btn-info fw-bold text-dark px-4 shadow">
                <i class="bi bi-plus-lg me-2"></i>THÊM VÉ MỚI
            </a>
        </div>

        {{-- Hiển thị thông báo thành công --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="profile-content shadow">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle border-secondary">
                    <thead>
                        <tr class="text-secondary border-bottom border-secondary">
                            <th width="10%">Ảnh</th>
                            <th width="25%">Tên trò chơi</th>
                            <th width="15%">Thể loại</th>
                            <th width="25%">Bảng giá vé</th> 
                            <th width="10%">Trạng thái</th>
                            <th width="15%" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                {{-- 1. Ảnh --}}
                                <td>
                                    <img src="{{ $ticket->image_url }}" alt="{{ $ticket->name }}" class="rounded shadow-sm"
                                        style="width: 80px; height: 50px; object-fit: cover;"
                                        onerror="this.src='https://via.placeholder.com/80x50?text=No+Image'">
                                </td>
                                
                                {{-- 2. Tên --}}
                                <td>
                                    <h6 class="fw-bold mb-0 ">{{ $ticket->name }}</h6>
                                    {{-- Hiển thị thêm tên cơ sở cho dễ quản lý --}}
                                    @if($ticket->location)
                                        <small class="text-while-50"><i class="bi bi-geo-alt-fill me-1 text-white-50"></i>{{ $ticket->location->name }}</small>
                                    @endif
                                </td>

                                {{-- 3. Thể loại --}}
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $ticket->category->name ?? 'Chưa phân loại' }}
                                    </span>
                                </td>

                                {{-- 4. GIÁ VÉ (ĐÃ SỬA: HIỆN 2 DÒNG) --}}
                                <td>
                                    {{-- Giá ngày thường --}}
                                    <div class="mb-2">
                                        <span class="badge bg-secondary border border-secondary text-white" style="width: 80px; font-weight: normal;">Thường</span>
                                        <span class="fw-bold text-success ms-2">
                                            {{ number_format($ticket->price) }} đ
                                        </span>
                                    </div>

                                    {{-- Giá cuối tuần --}}
                                    <div>
                                        <span class="badge bg-warning text-dark border border-warning" style="width: 80px; font-weight: normal;">Cuối tuần</span>
                                        <span class="fw-bold text-warning ms-2">
                                            @if($ticket->price_weekend && $ticket->price_weekend > 0)
                                                {{ number_format($ticket->price_weekend) }} đ
                                            @else
                                                <span class="text-muted fst-italic" style="font-size: 0.9em;">(Như thường)</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>

                                {{-- 5. Trạng thái --}}
                                <td>
                                    @if($ticket->status == 'active')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-octagon me-1"></i>Bảo trì</span>
                                    @endif
                                </td>
                                
                                {{-- 6. Thao tác --}}
                                <td class="text-end">
                                    <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="btn btn-sm btn-outline-warning me-2" title="Sửa vé">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('admin.tickets.delete', $ticket->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vé này không? Hành động này không thể hoàn tác!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa vé">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($tickets->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">Chưa có vé nào trong hệ thống.</p>
                </div>
            @endif
            
            <div class="mt-3 pb-4 d-flex justify-content-center">
        <div style="width: auto;">
            {{ $tickets->links() }}
        </div>
    </div>
        </div>
    </div>
@endsection