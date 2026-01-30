@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-5">
        <div class="bg-warning me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-warning fw-bold text-uppercase mb-0 letter-spacing-2">
            Cập nhật vé: {{ $ticket->name }}
        </h2>
    </div>

    <div class="profile-content shadow-lg p-4 bg-dark rounded">
        <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')
            
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
                    <input type="text" name="name" class="form-control" 
                           value="{{ old('name', $ticket->name) }}" required>
                </div>

                {{-- 2. Thể loại --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-white">Thể loại</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $ticket->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. CƠ SỞ (SỬA LẠI: Dùng Input + Datalist để sửa tên hoặc tạo mới) --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-white">Cơ sở / Địa điểm</label>
                    
                    {{-- Input: Hiển thị tên cơ sở hiện tại vào value --}}
                    <input type="text" 
                           name="location_name" 
                           class="form-control bg-dark text-white border-secondary" 
                           list="locationOptions" 
                           value="{{ old('location_name', $ticket->location->name ?? '') }}"
                           placeholder="Nhập tên cơ sở..." 
                           required autocomplete="off">
                    
                    {{-- Danh sách gợi ý --}}
                    <datalist id="locationOptions">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->name }}">
                        @endforeach
                    </datalist>
                    <div class="form-text text-muted" style="font-size: 0.8em">
                        <i class="bi bi-info-circle"></i> Nhập tên mới để tạo cơ sở mới.
                    </div>
                </div>
            </div>

            {{-- 4. Link ảnh & Preview --}}
            <div class="row mb-4">
                <div class="col-md-9">
                    <label class="form-label text-white">Link ảnh (URL)</label>
                    <textarea name="image_url" class="form-control" rows="3" required>{{ old('image_url', $ticket->image_url) }}</textarea>
                </div>
                <div class="col-md-3 text-center">
                    <label class="form-label text-white mb-2">Ảnh hiện tại</label>
                    <div class="border border-secondary p-1 rounded bg-secondary">
                        <img src="{{ $ticket->image_url }}" class="img-fluid rounded" 
                             style="max-height: 85px;" 
                             onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- 5. Giá vé --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-white">Giá Ngày Thường</label>
                    {{-- number_format để bỏ số thập phân thừa --}}
                    <input type="number" name="price" class="form-control" 
                           value="{{ old('price', number_format($ticket->price, 0, '', '')) }}" required>
                </div>

                <div class="col-md-3 mb-4">
                    <label class="form-label text-warning">Giá Cuối Tuần</label>
                    <input type="number" name="price_weekend" class="form-control" 
                           value="{{ old('price_weekend', number_format($ticket->price_weekend, 0, '', '')) }}"
                           placeholder="Để trống nếu bằng giá thường">
                </div>

                {{-- 6. Thời lượng --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-white">Thời lượng (Phút)</label>
                    <input type="number" name="duration" class="form-control" 
                           value="{{ old('duration', $ticket->duration) }}">
                </div>

                {{-- 7. Trạng thái --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-white">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ $ticket->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="maintenance" {{ $ticket->status == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                    </select>
                </div>
            </div>
            
            {{-- 8. Mô tả --}}
            <div class="mb-4">
                <label class="form-label text-white">Mô tả trò chơi</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $ticket->description) }}</textarea>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-warning fw-bold text-dark px-5">CẬP NHẬT</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary px-5">HỦY</a>
            </div>
        </form>
    </div>
</div>
@endsection