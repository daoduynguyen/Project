<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1622979135228-5b7964b4f53f?q=80&w=2000&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .glass-card {
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 40px;
            width: 100%; max-width: 450px;
            color: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            box-shadow: none; border-color: #0dcaf0;
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-info text-uppercase">Đăng nhập</h2>
            <p class="text-secondary small">Chào mừng trở lại Holomia VR</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="alert alert-danger py-2 small bg-danger bg-opacity-25 text-light border-danger border-opacity-50">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Email hoặc mật khẩu chưa đúng.
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required value="{{ old('email') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input bg-dark border-secondary" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-secondary small" for="remember">Ghi nhớ tôi</label>
                </div>
                <a href="#" class="text-info small text-decoration-none">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn btn-info w-100 fw-bold text-uppercase py-2 mb-3">Đăng nhập</button>
            
            <div class="text-center">
                <span class="text-secondary">Chưa có tài khoản?</span>
                <a href="{{ route('register') }}" class="text-info text-decoration-none fw-bold ms-1">Đăng ký ngay</a>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="text-secondary small text-decoration-none"><i class="bi bi-arrow-left"></i> Về trang chủ</a>
            </div>
        </form>
    </div>
</body>
</html>