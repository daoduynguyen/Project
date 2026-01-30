<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-dark text-white">
    @include('partials.navbar')

    <div class="container py-5">
        <h2 class="text-info fw-bold mb-4 text-uppercase"><i class="bi bi-credit-card"></i> Xác nhận thanh toán</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-25 text-white border-0 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

      <form action="{{ route('booking.confirm') }}" method="POST">
    @csrf
    
    {{-- 1. Input ID vé (Ẩn) --}}
    <input type="hidden" name="ticket_id" value="{{ $ticket_id ?? '' }}">

    {{-- 2. Dữ liệu giá (Ẩn) - Chú ý dấu > nằm ở cuối cùng --}}
    <div id="price-data" 
         data-week="{{ $totalWeek ?? 0 }}" 
         data-weekend="{{ $totalWeekend ?? 0 }}"
         style="display: none;"> 
    </div>

            <div class="row g-4 checkout-container">
                
                {{-- CỘT TRÁI: THÔNG TIN --}}
                <div class="col-lg-7 checkout-left">
                    <div class="card bg-secondary text-white border-0 rounded-4 p-4 mb-4 shadow">
                        <h5 class="fw-bold mb-3 border-bottom border-dark pb-2 text-white">Thông tin khách hàng</h5>
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                           <input type="text" name="customer_phone" class="form-control" 
       value="{{ Auth::check() ? Auth::user()->phone : '' }}" 
       required placeholder="Nhập SĐT để nhận vé">
                        </div>
                        
                        {{-- CHỌN NGÀY CHƠI --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-info">
                                <i class="bi bi-calendar-check me-1"></i> Bạn muốn chơi ngày nào?
                            </label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control" required>
                            
                            {{-- Khu vực hiển thị thông báo giá thay đổi --}}
                            <div id="date-message" class="mt-2 small">
                                <span class="text-secondary fst-italic">* Vé ngày thường (T2-T6), Vé cuối tuần (T7, CN).</span>
                            </div>
                        </div>

                        <div class="mb-3">        <label class="form-label">Địa chỉ mặc định</label>
                            <textarea name="shipping_address" class="form-control" rows="2" required placeholder="VD: 58 Trương Định...">{{ Auth::check() ? Auth::user()->address : '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú đơn hàng</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="VD: Gọi trước khi giao..."></textarea>
                        </div>
                    </div>

                    <div class="card bg-secondary text-white border-0 rounded-4 p-4 shadow">
                        <h5 class="fw-bold mb-3 border-bottom border-dark pb-2 text-white">Phương thức thanh toán</h5>
                        <div class="form-check mb-3 p-3 border border-dark rounded bg-dark bg-opacity-25">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label d-flex align-items-center gap-2" for="cod">
                                <i class="bi bi-cash-coin text-success fs-4"></i>
                                <div>
                                    <strong class="text-white">Thanh toán tại quầy (COD)</strong><br>
                                    <small class="text-white opacity-75">Thanh toán tiền mặt khi đến chơi.</small>
                                </div>
                            </label>
                        </div>
                        <div class="form-check p-3 border border-dark rounded bg-dark bg-opacity-25">
                            <input class="form-check-input" type="radio" name="payment_method" id="banking" value="banking">
                            <label class="form-check-label d-flex align-items-center gap-2" for="banking">
                                <i class="bi bi-qr-code-scan text-info fs-4"></i>
                                <div>
                                    <strong class="text-white">Chuyển khoản / Ví điện tử</strong><br>
                                    <small class="text-white opacity-75">Quét mã QR thanh toán nhanh.</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: ĐƠN HÀNG --}}
                <div class="col-lg-5">
                    <div class="checkout-right sticky-top" style="top: 100px;">
                        <div class="card bg-black border border-secondary rounded-4 p-4 shadow-lg">
                            <h5 class="fw-bold mb-4 text-center text-white">ĐƠN HÀNG CỦA BẠN</h5>
                            
                            <div class="cart-items-scroll mb-3 p-2" style="max-height: 300px; overflow-y: auto;">
                                @php $total = 0; @endphp
                                @foreach($cart as $id => $item)
                                    @php 
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal; 
                                    @endphp
                                    
                                    {{-- DÒNG SẢN PHẨM --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-3 item-row" 
                                         data-id="{{ $id }}" style="border-opacity: 0.2;">
                                        
                                        {{-- 1. Ảnh và Tên --}}
                                        <div class="d-flex align-items-center gap-3" style="flex: 1;">
                                            <img src="{{ Str::startsWith($item['image'], 'http') ? $item['image'] : asset('storage/' . $item['image']) }}" 
     class="rounded" width="50" height="50" style="object-fit: cover;"
     onerror="this.src='https://via.placeholder.com/50?text=Logo'">
                                            <div style="line-height: 1.2;">
                                                <h6 class="mb-0 text-white fw-bold text-truncate" style="max-width: 130px;">
                                                    {{ $item['name'] }}
                                                </h6>
                                                {{-- Giá hiển thị ở đây là giá lúc thêm vào giỏ, có thể khác giá thanh toán thực tế nếu chọn cuối tuần --}}
                                                <small class="text-secondary" style="font-size: 0.85rem;">{{ number_format($item['price']) }}đ</small>
                                            </div>
                                        </div>

                                        {{-- 2. Số lượng --}}
                                        <div class="px-2">
                                            <div class="input-group input-group-sm" style="width: 70px;">
                                                <input type="number" 
                                                       class="form-control text-center bg-secondary bg-opacity-25 text-white border-secondary update-cart" 
                                                       value="{{ $item['quantity'] }}" min="1">
                                            </div>
                                        </div>

                                        {{-- 3. Thành tiền --}}
                                        <span class="fw-bold text-white ms-2 item-subtotal">{{ number_format($subtotal) }}đ</span>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="border-secondary">
                            
                            {{-- TỔNG CỘNG --}}
                            <div class="bg-secondary bg-opacity-10 p-3 rounded border border-secondary border-opacity-25">
                                <div class="d-flex justify-content-between mb-2 text-white-50">
                                    <span>Tạm tính:</span>
                                    {{-- Giá trị mặc định là Ngày thường ($totalWeekday) --}}
                                    <span class="fw-bold text-white" id="temp-total" data-amount="{{ $totalWeek ?? $total }}">
                                        {{ number_format($totalWeek ?? $total) }}đ
                                    </span>
                                </div>
                            
                                <div class="d-flex justify-content-between mb-2 text-success" id="discount-row" style="display: none;">
                                    <span><i class="bi bi-gift-fill me-1"></i> Giảm giá:</span>
                                    <span class="fw-bold">-<span id="discount-amount">0</span>đ</span>
                                </div>
                            
                                <div class="mb-3 mt-3">
                                    <label class="text-white mb-2 small"><i class="bi bi-ticket-perforated me-1"></i> Mã ưu đãi</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="coupon_code" class="form-control bg-dark text-white border-secondary" placeholder="Nhập mã...">
                                        <button class="btn btn-info fw-bold" type="button" id="btn-apply-coupon">Áp dụng</button>
                                    </div>
                                    <div id="coupon-message" class="mt-2 small"></div>
                                </div>

                                <hr class="border-secondary opacity-25 my-2">
                            
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white text-uppercase fw-bold">Tổng thanh toán:</span>
                                    {{-- Tổng tiền hiển thị cuối cùng --}}
                                    <span class="fs-4 fw-bold text-info" id="final-total">{{ number_format($totalWeek ?? $total) }}đ</span>
                                </div>
                            
                                <input type="hidden" name="discount_amount" id="input_discount" value="0">
                                <input type="hidden" name="coupon_code" id="input_coupon_code" value="">
                            </div>

                            <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-uppercase rounded-pill shadow mt-4">
                                <i class="bi bi-check-circle-fill me-2"></i> Xác nhận đặt hàng
                            </button>
                            
                            <a href="{{ route('ticket.shop') }}" class="btn btn-link text-white opacity-75 w-100 mt-2 text-decoration-none small">
                                <i class="bi bi-arrow-left"></i> Chọn thêm vé khác
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- SCRIPT XỬ LÝ: CHỌN NGÀY -> ĐỔI GIÁ & VOUCHER --}}
   <script>
$(document).ready(function() {
    
    // --- 1. CẤU HÌNH & HÀM DÙNG CHUNG ---
    
    // Hàm định dạng tiền tệ (VD: 200000 -> 200.000đ)
    const formatMoney = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    };

    // Chặn chọn ngày quá khứ
    var today = new Date().toISOString().split('T')[0];
    $('#booking_date').attr('min', today);


    // --- 2. XỬ LÝ KHI ĐỔI NGÀY (Cập nhật giá & Check lại Voucher) ---
    $('#booking_date').on('change', function() {
        var inputVal = $(this).val(); 
        if (!inputVal) return;

        // Fix lỗi múi giờ: Dùng getUTCDay để lấy thứ chính xác
        var dateObj = new Date(inputVal);
        var dayOfWeek = dateObj.getUTCDay(); // 0=CN, 6=T7

        var isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);

        // Lấy giá tiền từ HTML (Lưu ý: Bạn đang đặt tên là data-week)
        var priceData = $('#price-data');
        var weekdayPrice = parseFloat(priceData.data('week')); 
        var weekendPrice = parseFloat(priceData.data('weekend'));

        // Chọn giá mới dựa trên ngày (Đã sửa lỗi sai tên biến weekPrice -> weekdayPrice)
        var newTotal = isWeekend ? weekendPrice : weekdayPrice;

        // Cập nhật thông báo
        var message = '';
        if(isWeekend) {
            message = '<span class="text-warning fw-bold"><i class="bi bi-exclamation-circle"></i> Giá CUỐI TUẦN (T7, CN)</span>';
        } else {
            message = '<span class="text-success fw-bold"><i class="bi bi-check-circle"></i> Giá NGÀY THƯỜNG (T2-T6)</span>';
        }
        $('#date-message').html(message);
        
        // Cập nhật giá hiển thị
        $('#temp-total').text(formatMoney(newTotal));
        $('#final-total').text(formatMoney(newTotal));
        
        // [QUAN TRỌNG] Cập nhật data-amount để Voucher đọc được giá mới
        $('#temp-total').data('amount', newTotal);

        // [TỰ ĐỘNG] Nếu đang áp mã giảm giá, hãy kích hoạt lại nút "Áp dụng" để tính lại tiền
        if ($('#coupon_code').val() && $('#discount-row').is(':visible')) {
            $('#btn-apply-coupon').click();
        }
    });


    // --- 3. XỬ LÝ CẬP NHẬT SỐ LƯỢNG GIỎ HÀNG ---
    $(".update-cart").change(function (e) {
        e.preventDefault();
        var ele = $(this);
        $.ajax({
            url: '{{ route('cart.update') }}', 
            method: "PATCH",
            data: {
                _token: '{{ csrf_token() }}', 
                id: ele.closest(".item-row").attr("data-id"), 
                quantity: ele.val()
            },
            success: function (response) {
                window.location.reload(); 
            }
        });
    });


    // --- 4. XỬ LÝ VOUCHER (Mã giảm giá) ---
    $('#btn-apply-coupon').click(function() {
        let code = $('#coupon_code').val();
        
        // Lấy tổng tiền (đã được cập nhật từ logic chọn ngày ở trên)
        let total = $('#temp-total').data('amount'); 
        let btn = $(this);

        // Reset thông báo cũ
        $('#coupon-message').html('');

        if(!code) {
            $('#coupon-message').html('<span class="text-danger">Vui lòng nhập mã!</span>');
            return;
        }

        // Hiệu ứng loading
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

        $.ajax({
            url: "{{ route('checkout.check_coupon') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                code: code,
                total_amount: total // Gửi tổng tiền mới nhất lên server
            },
            success: function(response) {
                btn.prop('disabled', false).text('ÁP DỤNG');

                if (response.status == 'success') {
                    // Thông báo thành công
                    $('#coupon-message').html('<span class="text-success fw-bold"><i class="bi bi-check-circle"></i> ' + response.message + '</span>');
                    
                    // Hiện dòng giảm giá
                    $('#discount-row').slideDown();
                    $('#discount-amount').text(formatMoney(response.discount));
                    
                    // Cập nhật tổng tiền cuối cùng
                    $('#final-total').text(formatMoney(response.new_total));

                    // Lưu vào input ẩn
                    $('#input_discount').val(response.discount);
                    $('#input_coupon_code').val(code);

                } else {
                    // Thông báo lỗi
                    let msg = response.message ? response.message : 'Mã không hợp lệ';
                    $('#coupon-message').html('<span class="text-danger"><i class="bi bi-x-circle"></i> ' + msg + '</span>');
                    
                    // Reset giao diện nếu lỗi
                    $('#discount-row').slideUp();
                    $('#input_discount').val(0);
                    $('#input_coupon_code').val('');
                    
                    // Trả về giá gốc (lấy từ data-amount)
                    $('#final-total').text(formatMoney(total));
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('ÁP DỤNG');
                console.log(xhr.responseText);
                $('#coupon-message').html('<span class="text-danger">Lỗi hệ thống!</span>');
            }
        });
    });

});
</script>
</body>
</html>