<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng vé - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    {{-- Thêm SweetAlert2 để thông báo đẹp --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* CSS cho nút tim */
        .btn-wishlist {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            transition: all 0.2s;
        }

        .btn-wishlist:hover {
            transform: scale(1.1);
            background: white;
        }
    </style>
</head>

<body class="bg-dark text-white">

    @include('partials.navbar')

    <div class="container py-5">

        <div class="text-center mb-5">
            <h2 class="text-info fw-bold text-uppercase display-6">
                <i class="bi bi-shop"></i> KHO VÉ TOÀN TẬP
            </h2>
            <p class="text-secondary">Hiện có tổng cộng {{ $tickets->total() }} trò chơi trong hệ thống</p>
            <div style="width: 60px; height: 3px; background-color: #0dcaf0; margin: 10px auto;"></div>
        </div>

        <div class="row g-4">
            @foreach($tickets as $ticket)
                <div class="col-md-4">
                    <div
                        class="card h-100 card-game text-white overflow-hidden rounded-3 {{ $ticket->status == 'maintenance' ? 'card-maintenance' : '' }}">

                        @if($ticket->status == 'maintenance')
                            <div class="badge-maintenance">BẢO TRÌ</div>
                        @endif

                        <div class="position-relative">
                            <img src="{{ $ticket->image_url }}" class="card-img-top"
                                style="height: 200px; object-fit: cover;">

                            {{-- Badge danh mục --}}
                            <span class="position-absolute top-0 start-0 m-2 badge bg-primary opacity-75">
                                {{ $ticket->category->name }}
                            </span>

                            {{-- NÚT THẢ TIM  --}}
                            <button
                                class="btn btn-light rounded-circle position-absolute top-0 end-0 m-2 shadow-sm btn-wishlist btn-toggle-wishlist-global"
                                data-id="{{ $ticket->id }}" title="Thêm vào yêu thích">
                                @if(Auth::check() && Auth::user()->favorites->contains($ticket->id))
                                    <i class="bi bi-heart-fill text-danger"></i>
                                @else
                                    <i class="bi bi-heart text-danger"></i>
                                @endif
                            </button>
                        </div>

                        <div class="card-body d-flex flex-column p-3 bg-dark">
                            <h5 class="card-title fw-bold text-truncate">{{ $ticket->name }}</h5>

                            <div class="d-flex justify-content-between small text-secondary mb-3">
                                <span><i class="bi bi-geo-alt"></i> {{ $ticket->location->name }}</span>
                                <span><i class="bi bi-star-fill text-warning"></i> {{ $ticket->avg_rating }}</span>
                            </div>

                            <div
                                class="mt-auto d-flex justify-content-between align-items-center bg-secondary bg-opacity-10 p-2 rounded">
                                <div>
                                    <span class="d-block small text-secondary" style="font-size: 0.7rem;">Ngày thường</span>
                                    <span class="fw-bold text-info">{{ number_format($ticket->price) }}đ</span>
                                </div>

                                @if($ticket->status == 'maintenance')
                                    <button class="btn btn-secondary btn-sm fw-bold shadow-sm" disabled>Tạm đóng</button>
                                @else
                                    {{-- TRỎ ĐẾN ROUTE booking.form --}}
                                    <a href="{{ route('booking.form', $ticket->id) }}"
                                        class="btn btn-info btn-sm fw-bold shadow-sm text-dark">
                                        <i class="bi bi-ticket-perforated"></i> Đặt vé ngay
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>

    </div>

    <footer class="bg-black text-center py-4 border-top border-secondary mt-5 text-muted">
        <p class="mb-0">© 2026 Holomia VR System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- [SCRIPT XỬ LÝ AJAX THẢ TIM] --}}
    <script>
        $(document).ready(function () {
            $('.btn-toggle-wishlist-global').click(function (e) {
                e.preventDefault();

                // Kiểm tra xem đã đăng nhập chưa (Nếu chưa thì báo lỗi)
                @if(!Auth::check())
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa đăng nhập',
                        text: 'Vui lòng đăng nhập để lưu danh sách yêu thích!',
                        confirmButtonText: 'Đăng nhập ngay',
                        confirmButtonColor: '#0dcaf0',
                        background: '#212529',
                        color: '#fff'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                    return;
                @endif

                let btn = $(this);
                let icon = btn.find('i');
                let id = btn.data('id');

                $.ajax({
                    url: '/wishlist/toggle/' + id,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.is_favorited) {
                            // Đổi thành tim đỏ
                            icon.removeClass('bi-heart').addClass('bi-heart-fill');

                            // Hiệu ứng Toast thông báo
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                background: '#212529',
                                color: '#fff'
                            });
                            Toast.fire({ icon: 'success', title: 'Đã thêm vào yêu thích' });
                        } else {
                            // Đổi về tim rỗng
                            icon.removeClass('bi-heart-fill').addClass('bi-heart');
                        }
                    },
                    error: function () {
                        alert('Có lỗi xảy ra, vui lòng thử lại!');
                    }
                });
            });
        });
    </script>
</body>

</html>