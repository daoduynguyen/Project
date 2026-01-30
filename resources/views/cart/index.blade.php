<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    @include('partials.navbar')

    <div class="container py-5">
        <h2 class="text-info fw-bold mb-4 text-uppercase"><i class="bi bi-cart3"></i> Giỏ hàng của bạn</h2>
{{-- HIỂN THỊ LỖI NẾU CÓ --}}
@if(session('error'))
    <div class="alert alert-danger bg-danger bg-opacity-25 text-white border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
@endif

        @if(count($cart) > 0)
            <div class="row g-4">
                {{-- CỘT TRÁI: DANH SÁCH SẢN PHẨM --}}
                <div class="col-lg-8">
                    <div class="card bg-secondary bg-opacity-10 border-secondary border-opacity-25 rounded-4 p-3">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-secondary small text-uppercase">
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach($cart as $id => $item)
                                        @php 
                                            $subtotal = $item['price'] * $item['quantity']; 
                                            $total += $subtotal;
                                        @endphp
                                        
                                        <tr data-id="{{ $id }}">
                                            {{-- 1. Tên & Ảnh --}}
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ Str::startsWith($item['image'], 'http') ? $item['image'] : asset($item['image']) }}" 
                                                         class="rounded" width="60" height="60" style="object-fit: cover;">
                                                    <h6 class="mb-0 fw-bold">{{ $item['name'] }}</h6>
                                                </div>
                                            </td>

                                            {{-- 2. Giá vé --}}
                                            <td>{{ number_format($item['price']) }}đ</td>

                                            {{-- 3. Ô NHẬP SỐ LƯỢNG (ĐÃ SỬA) --}}
                                            <td>
                                                <input type="number" value="{{ $item['quantity'] }}" 
                                                       class="form-control text-center update-cart bg-dark text-white border-secondary" 
                                                       style="width: 70px;" min="1">
                                            </td>

                                            {{-- 4. Thành tiền (THÊM CLASS item-subtotal) --}}
                                            <td class="text-end fw-bold text-info item-subtotal">
                                                {{ number_format($subtotal) }}đ
                                            </td>
                                            
                                            {{-- 5. Nút xóa --}}
                                            <td class="text-end">
                                                <a href="{{ route('cart.remove', $id) }}" class="text-danger fs-5" onclick="return confirm('Bạn chắc chắn muốn xóa vé này?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('ticket.shop') }}" class="text-info text-decoration-none small">
                            <i class="bi bi-arrow-left"></i> Tiếp tục chọn vé
                        </a>
                    </div>
                </div>

                {{-- CỘT PHẢI: TỔNG TIỀN & THANH TOÁN --}}
                <div class="col-lg-4">
    <div class="card bg-black border border-secondary border-opacity-50 rounded-4 p-4 sticky-top" style="top: 100px;">
        <h5 class="fw-bold mb-4 border-bottom border-secondary pb-3 text-white">Thanh toán</h5>
        
        {{-- 1. Tính lại tổng tiền gốc từ giỏ hàng (cho chắc chắn) --}}
        @php 
            $total = 0;
            if(session('cart')) {
                foreach(session('cart') as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
            }
        @endphp

        {{-- 2. Hiển thị Tổng tiền gốc --}}
        <div class="d-flex justify-content-between mb-2">
            <span class="text-secondary">Tổng tiền vé:</span>
            <span class="fw-bold text-white">{{ number_format($total) }}đ</span>
        </div>

        {{-- 3. LOGIC HIỂN THỊ MÃ GIẢM GIÁ --}}
        @if(session()->has('coupon'))
            @php
                $coupon = session('coupon');
                $discount = $coupon['discount'];
                // Đảm bảo không giảm quá số tiền gốc
                if($discount > $total) $discount = $total;
                $finalTotal = $total - $discount;
            @endphp

            {{-- Dòng hiển thị số tiền được giảm --}}
            <div class="d-flex justify-content-between text-success mb-2">
                <span>
                    <i class="bi bi-ticket-perforated-fill"></i> Mã {{ $coupon['code'] }}:
                </span>
                <span>-{{ number_format($discount) }}đ</span>
            </div>

            {{-- Nút xóa mã (cần thêm Route này) --}}
            <div class="text-end mb-3">
                <a href="{{ route('coupon.remove') }}" class="text-danger small text-decoration-none" style="font-size: 0.85rem;">
                    <i class="bi bi-x-circle"></i> Gỡ mã
                </a>
            </div>

            <hr class="border-secondary opacity-50">

            {{-- Hiển thị TỔNG TIỀN SAU KHI GIẢM --}}
            <div class="d-flex justify-content-between mb-3">
                <span class="text-white fw-bold text-uppercase">Cần thanh toán:</span>
                <span class="fs-4 fw-bold text-info" id="grand-total">{{ number_format($finalTotal) }}đ</span>
            </div>

        @else
            {{-- TRƯỜNG HỢP KHÔNG CÓ MÃ --}}
            <div class="d-flex justify-content-between mb-3">
                <span class="text-white fw-bold">Thanh toán:</span>
                {{-- ID grand-total giữ nguyên để JS cập nhật nếu cần --}}
                <span class="fs-5 fw-bold text-white" id="grand-total">{{ number_format($total) }}đ</span>
            </div>
        @endif
        
        <p class="small text-secondary mb-4 fst-italic">Phí dịch vụ và thuế sẽ được tính ở bước tiếp theo.</p>

        {{-- Form Submit --}}
        <form action="{{ route('payment.final') }}" method="POST">
            @csrf
            {{-- Nếu có mã giảm giá thì gửi kèm hidden input (Dù session đã lưu nhưng gửi kèm cho chắc) --}}
            @if(session()->has('coupon'))
                <input type="hidden" name="coupon_code" value="{{ session('coupon')['code'] }}">
            @endif

            <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-uppercase rounded-pill shadow">
                XÁC NHẬN THANH TOÁN <i class="bi bi-check-circle-fill ms-2"></i>
            </button>
        </form>
        
        <div class="text-center mt-3">
            <a href="{{ route('cart.clear') }}" class="text-secondary small text-decoration-none hover-text-white">
                <i class="bi bi-trash"></i> Xóa sạch giỏ hàng
            </a>
        </div>
    </div>
</div>
            </div>
        @else 
            {{-- GIỎ HÀNG TRỐNG --}}
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-secondary opacity-25"></i>
                <h3 class="mt-4 fw-bold">Giỏ hàng đang trống</h3>
                <p class="text-secondary">Bạn chưa chọn bất kỳ trò chơi nào để trải nghiệm.</p>
                <a href="{{ route('ticket.shop') }}" class="btn btn-info px-5 py-3 rounded-pill fw-bold mt-3">
                    XEM DANH SÁCH TRÒ CHƠI
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            
            // --- 1. CODE ẨN THÔNG BÁO SAU 3 GIÂY (MỚI) ---
            if ($(".alert").length) {
                setTimeout(function() {
                    $(".alert").slideUp(500, function() {
                        $(this).remove();
                    }); 
                }, 3000); // 3000ms = 3 giây
            }
            $(".update-cart").change(function (e) {
                e.preventDefault();
        
                var ele = $(this);
                var row = ele.parents("tr"); // Lấy dòng hiện tại
                var id = row.attr("data-id"); // Lấy ID sản phẩm
                var quantity = ele.val(); // Lấy số lượng mới

                // Gửi dữ liệu lên Server
                $.ajax({
                    url: '{{ route('cart.update') }}',
                    method: "PATCH",
                    data: {
                        _token: '{{ csrf_token() }}', 
                        id: id, 
                        quantity: quantity
                    },
                    success: function (response) {
                        // Cập nhật thành tiền của dòng đó
                        row.find(".item-subtotal").text(response.item_subtotal);
                        
                        // Cập nhật tổng tiền cả giỏ hàng
                        $("#grand-total").text(response.grand_total);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText); // Log lỗi nếu có
                    }
                });
            });
        });
    </script>
</body>
</html>