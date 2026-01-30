<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Holomia VR</title>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- CUSTOM CSS CHO SIDEBAR ADMIN --}}
    <style>
        body {
            background-color: #121212;
            /* Nền tổng tối hơn chút cho đỡ mỏi mắt */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* 1. Khung Sidebar */
        .sidebar-container {
            width: 280px;
            background: #0f0f0f;
            /* Đen sâu */
            border-right: 1px solid #2d2d2d;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            height: 100vh;
            /* Cố định chiều cao bằng màn hình */
            overflow-y: auto;
            /* Cho phép cuộn nếu menu dài */
        }

        /* 2. Logo */
        .sidebar-brand {
            padding: 2.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #2d2d2d;
            margin-bottom: 1.5rem;
        }

        .sidebar-brand h3 {
            font-size: 1.5rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0;
            color: #ffffff;
        }

        .sidebar-brand .highlight {
            color: #0dcaf0;
            /* Màu Cyan */
            font-weight: 800;
        }

        /* 3. Menu Item */
        .nav-link {
            color: #a0a0a0 !important;
            /* Màu xám nhạt mặc định */
            padding: 12px 24px;
            margin-bottom: 4px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
            /* Chuẩn bị cho border active */
        }

        /* QUAN TRỌNG: Cố định độ rộng icon để chữ luôn thẳng hàng */
        .nav-link i {
            width: 30px;
            font-size: 1.1rem;
            text-align: center;
            margin-right: 10px;
            transition: 0.3s;
        }

        /* Hiệu ứng khi di chuột (Hover) */
        .nav-link:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.05);
            padding-left: 28px;
            /* Đẩy nhẹ sang phải */
        }

        .nav-link:hover i {
            color: #0dcaf0;
        }

        /* Trạng thái đang chọn (Active) */
        .nav-link.active {
            color: #0dcaf0 !important;
            background: linear-gradient(90deg, rgba(13, 202, 240, 0.1) 0%, rgba(0, 0, 0, 0) 100%);
            border-left: 3px solid #0dcaf0;
        }

        .nav-link.active i {
            color: #0dcaf0;
            transform: scale(1.1);
        }

        /* Nút đăng xuất */
        .logout-container {
            margin-top: auto;
            padding: 1.5rem;
            border-top: 1px solid #2d2d2d;
        }

        .btn-logout {
            border: 1px solid #dc3545;
            color: #dc3545;
            background: transparent;
            font-weight: 600;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .btn-logout:hover {
            background: #dc3545;
            color: white;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.4);
        }
    </style>
</head>

<body class="text-white">

    <div class="d-flex">

        {{-- === SIDEBAR === --}}
        <div class="sidebar-container">

            {{-- LOGO --}}
            <div class="sidebar-brand">
                <h3>Holomia <br><span class="highlight">Admin</span></h3>
            </div>

            {{-- MENU --}}
            <div class="flex-grow-1 px-0">
                <ul class="nav flex-column">

                    {{-- 1. Dashboard --}}
                    {{-- Logic: Nếu đang ở trang dashboard thì thêm class 'active' --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Thống kê</span>
                        </a>
                    </li>

                    {{-- 2. Quản lý Vé --}}
                    {{-- Logic: Nếu đang ở bất kỳ trang nào của vé (index, create, edit...) thì active --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.tickets.index') }}"
                            class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                            <i class="bi bi-ticket-perforated"></i>
                            <span>Quản lý vé</span>
                        </a>
                    </li>

                    {{-- 3. Duyệt đơn hàng --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.bookings.index') }}"
                            class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                            <i class="bi bi-cart-check"></i>
                            <span>Duyệt đơn hàng</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.coupons.index') }}">
                            <i class="bi bi-ticket-perforated-fill"></i>
                            <span>Kho Voucher</span>
                        </a>
                    </li>

                    {{-- 4. Người dùng --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.users') }}"
                            class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            <span>Người dùng</span>
                        </a>
                    </li>

                    {{-- 5. Tin nhắn --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.contacts') }}"
                            class="nav-link {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
                            <i class="bi bi-chat-dots"></i>
                            <span>Tin nhắn</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- FOOTER (LOGOUT) --}}
            <div class="logout-container">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-logout w-100 py-2 rounded-3">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
        {{-- === END SIDEBAR === --}}

        {{-- === MAIN CONTENT === --}}
        <div class="flex-grow-1" style="background-color: #121212; min-height: 100vh;">
            <div class="p-5">
                @yield('admin_content')
            </div>
        </div>

    </div>

    {{-- Script JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script tự động ẩn thông báo sau 3 giây --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                // Tìm tất cả các thông báo (alert)
                var alerts = document.querySelectorAll('.alert');

                alerts.forEach(function (alert) {
                    // Sử dụng Bootstrap API để đóng alert mượt mà
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 3000); // 3000ms = 3 giây
        });
    </script>
</body>

</html>
</body>

</html>