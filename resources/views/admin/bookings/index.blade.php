@extends('layouts.admin')
{{-- Adminn quản lý đơn hàng --}}
@section('admin_content')
    <div class="container-fluid">
        {{-- Tiêu đề --}}
        <div class="d-flex align-items-center mb-4">
            <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
            <h2 class="text-info fw-bold text-uppercase mb-0 letter-spacing-2">Quản lý Đơn hàng</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="profile-content shadow bg-dark text-light border border-secondary rounded">
            <div class="card-body p-0">
                <div class="table-responsive">
                    {{-- Bảng --}}
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead class="bg-dark text-white">
                            <tr class="text-secondary border-bottom border-secondary text-uppercase small">
                                {{-- 1. Mã đơn: Thu nhỏ còn 5% và căn giữa --}}
                                <th width="6%" class="text-center">Mã</th>
                                {{-- 2. Khách hàng: 15% --}}
                                <th width="22%" class="text-center">Khách hàng</th>
                                {{-- 3. Chi tiết: Tăng lên 35% (Quan trọng nhất) --}}
                                <th width="27%">Chi tiết đơn</th>
                                {{-- 4. Tổng tiền: 12% --}}
                                <th width="12%" class="text-center">Tổng tiền</th>
                                {{-- 5. Ngày đặt: 10% --}}
                                <th width="10%" class="text-center">Ngày đặt</th>

                                {{-- 6. Trạng thái: 15% và căn giữa --}}
                                <th width="15%" class="text-center">Trạng thái</th>

                                {{-- 7. Hành động: 8% và căn phải --}}
                                <th width="8%" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr class="border-bottom border-secondary">
                                    {{-- 1. Mã đơn --}}
                                    <td class="fw-bold text-info ps-4">#{{ $booking->id }}</td>

                                    {{-- 2. Thông tin khách --}}
                                    <td>
                                        <div class="fw-bold text-white">
                                            {{ $booking->user->name ?? $booking->customer_name ?? 'Khách vãng lai' }}
                                        </div>
                                        {{-- Email: Đổi text-muted thành text-white-50 (Trắng mờ) --}}
                                        <div class="small text-white-50">
                                            <i class="bi bi-envelope me-1"></i>
                                            {{ $booking->user->email ?? $booking->customer_email ?? '---' }}
                                        </div>
                                    </td>

                                    {{-- 3. Chi tiết vé --}}
                                    <td>
                                        @if($booking->details && $booking->details->count() > 0)
                                            @foreach($booking->details as $detail)
                                                <div class="mb-1 small d-flex align-items-center">
                                                    <i class="bi bi-ticket-perforated-fill text-warning me-2"></i>
                                                    <span
                                                        class="text-light me-2">{{ $detail->ticket->name ?? 'Vé không tồn tại' }}</span>
                                                    <span class="badge bg-secondary border border-light">x{{ $detail->quantity }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-white-50 fst-italic">Không có chi tiết</span>
                                        @endif
                                    </td>

                                    {{-- 4. Tổng tiền --}}
                                    <td class="fw-bold text-success fs-6">{{ number_format($booking->total_amount) }} đ</td>

                                    {{-- 5. Ngày đặt --}}
                                    <td class="small text-white-50">
                                        <div>{{ $booking->created_at->format('d/m/Y') }}</div>
                                        <div>{{ $booking->created_at->format('H:i') }}</div>
                                    </td>

                                    {{-- 6. Trạng thái --}}
                                    <td>
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-select form-select-sm fw-bold border-0 shadow-none
                                                @if($booking->status == 'pending') bg-warning text-dark 
                                                @elseif($booking->status == 'paid') bg-success text-white 
                                                @elseif($booking->status == 'cancelled') bg-danger text-white 
                                                @else bg-secondary text-white @endif" onchange="this.form.submit()"
                                                style="width: 145px; cursor: pointer;">

                                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>⏳
                                                    Chờ xử lý</option>
                                                <option value="paid" {{ $booking->status == 'paid' ? 'selected' : '' }}>✅ Duyệt</option>
                                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>
                                                    ❌ Đã hủy</option>
                                            </select>
                                        </form>
                                    </td>

                                    {{-- 7. Xóa --}}
                                    <td class="text-end pe-4">
                                        <form action="{{ route('admin.bookings.delete', $booking->id) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa đơn này vĩnh viễn?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Xóa đơn hàng">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-white-50">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                            <p>Chưa có đơn hàng nào trong hệ thống.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang (Nếu có) --}}
                <div class="d-flex justify-content-center w-100 mt-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection