@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-5">
        <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-info fw-bold text-uppercase mb-0 letter-spacing-2">Thêm vé VR mới</h2>
    </div>

    <div class="profile-content shadow-lg p-4 bg-dark rounded">
        <form action="{{ route('admin.tickets.store') }}" method="POST">
            @csrf
            
            {{-- Hiển thị lỗi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                {{-- 1. Tên trò chơi --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label text-white">Tên trò chơi VR</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Zombie VR" required>
                </div>

                {{-- 2. Thể loại --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-white">Thể loại</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. CƠ SỞ (SỬA LẠI: Dùng Input để tự tạo mới) --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-white">Cơ sở / Địa điểm</label>
                    {{-- QUAN TRỌNG: name="location_name" --}}
                    <input type="text" 
                           name="location_name" 
                           class="form-control bg-dark text-white border-secondary" 
                           list="locationOptions" 
                           placeholder="Nhập tên cơ sở..." 
                           required autocomplete="off">
                    
                    <datalist id="locationOptions">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->name }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            {{-- 4. Link Ảnh --}}
            <div class="mb-4">
                <label class="form-label text-white">Link ảnh (URL)</label>
                <textarea name="image_url" class="form-control" rows="2" placeholder="https://..." required></textarea>
            </div>

            <div class="row">
                {{-- 5. GIÁ VÉ (SỬA LẠI NAME CHO KHỚP) --}}
                
                {{-- Giá Ngày Thường -> name="price" (Để khớp với cột gốc trong DB) --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label text-white">Giá Ngày Thường</label>
                    <input type="number" name="price" class="form-control" placeholder="100000" required>
                </div>

                {{-- Giá Cuối Tuần -> name="price_weekend" --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label text-warning">Giá Cuối Tuần</label>
                    <input type="number" name="price_weekend" class="form-control" placeholder="Để trống nếu bằng giá thường">
                </div>

                {{-- 6. Thời lượng --}}
                <div class="col-md-4 mb-4">
                    <label class="form-label text-white">Thời lượng (Phút)</label>
                    <input type="number" name="duration" class="form-control" value="30">
                </div>
            </div>

            {{-- 7. Trạng thái --}}
            <div class="mb-4">
                <label class="form-label text-white">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="active">Hoạt động</option>
                    <option value="maintenance">Bảo trì</option>
                </select>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-info fw-bold text-dark px-5">LƯU VÉ MỚI</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary px-5">HỦY</a>
            </div>
        </form>
    </div>
</div>
@endsection