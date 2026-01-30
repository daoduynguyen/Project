# 🎮 Holomia VR - Hệ Thống Đặt Vé Khu Vui Chơi Thực Tế Ảo

![Holomia Banner](public/images/banner.jpg) ## 📝 Giới thiệu
**Holomia VR** là nền tảng website thương mại điện tử chuyên cung cấp dịch vụ đặt vé trải nghiệm các trò chơi thực tế ảo (Virtual Reality). Hệ thống được xây dựng trên nền tảng **Laravel Framework**, cung cấp giải pháp toàn diện từ việc khách hàng chọn vé, thanh toán online cho đến trang quản trị (Admin Dashboard) mạnh mẽ để theo dõi doanh thu và đơn hàng.

## 🚀 Công nghệ sử dụng
Dự án được phát triển dựa trên các công nghệ hiện đại:

* **Backend:** PHP 8.2, Laravel Framework (MVC Architecture).
* **Frontend:** HTML5, CSS3, Bootstrap 5 (Giao diện Dark Mode Gaming).
* **Scripting:** jQuery, AJAX (Xử lý giỏ hàng và thanh toán mượt mà).
* **Database:** MySQL.
* **Mail:** SMTP (Gửi mail xác nhận và phản hồi liên hệ).

## ✨ Chức năng nổi bật

### 1. Phân hệ Khách hàng (Client)
* **Trang chủ & Giới thiệu:** Giao diện tối hiện đại, hiển thị Tầm nhìn & Sứ mệnh, danh sách trò chơi nổi bật.
* **Chi tiết vé:** Xem thông tin trò chơi, đánh giá, thời lượng.
    * 🔥 **Logic giá động:** Tự động thay đổi giá vé theo **Ngày thường (T2-T6)** hoặc **Cuối tuần (T7-CN)**.
* **Giỏ hàng thông minh:**
    * Thêm/Sửa/Xóa vé bằng AJAX (không load lại trang).
    * Áp dụng **Mã giảm giá (Voucher/Coupon)** trực tiếp.
* **Thanh toán:**
    * Hỗ trợ phương thức: COD (Tại quầy) và Chuyển khoản ngân hàng.
    * Xác nhận đơn hàng và gửi thông tin về hệ thống.

### 2. Phân hệ Quản trị (Admin)
* **Dashboard thống kê:**
    * Biểu đồ doanh thu 7 ngày gần nhất.
    * Biểu đồ tròn tỷ lệ trạng thái đơn hàng.
    * Thẻ thống kê tổng quan (Doanh thu, Đơn chờ, Tổng vé, Khách hàng).
* **Quản lý Vé (Tickets):** Thêm, sửa, xóa, cập nhật giá vé ngày thường/cuối tuần.
* **Quản lý Đơn hàng (Orders):** Xem chi tiết, duyệt đơn hàng (chuyển trạng thái từ Pending -> Paid).
* **Quản lý Liên hệ (Contacts):** Nhận tin nhắn từ khách và gửi Email phản hồi trực tiếp từ trang Admin.
* **Quản lý Users:** Quản lý danh sách khách hàng.

## 📸 Hình ảnh demo

| Trang chủ | 
| [Home](public/demo/home.png) | 

| Xem chi tiết | 
| [Detail](public/demo/xemchitiet.png) |

| Giới thiệu | 
| [about](public/demo/Gioithieu.png) |

| Liên hệ | 
| [contact](public/demo/lienhe.png) |

| Đặt vé | 
| [shop](public/demo/datve.png) |

| Giỏ hàng |
| [GioHang](public/demo/giohang.png) |

| Hồ sơ cá nhân | 
| [Profile](public/demo/hosocanhan.png) |

| Admin Dashboard |
 ![Admin](public/demo/admin.png) |


**Clone dự án**
git clone [https://github.com/daoduynguyen/BookingVrHolomia.git]