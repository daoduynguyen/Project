document.addEventListener('DOMContentLoaded', function () {
    const data = document.getElementById('payment-data').dataset;

    if (data.status === 'success') {
        if (data.method === 'banking') {
            Swal.fire({
                title: 'Mua vé thành công!',
                text: 'Cảm ơn bạn đã lựa chọn Holomia VR.',
                icon: 'success',
                confirmButtonColor: '#0dcaf0'
            });
        } else {
            Swal.fire({
                title: 'Đặt vé thành công!',
                html: `Bạn hãy chuẩn bị <b>${data.amount}đ</b> để thanh toán tại quầy nhé.`,
                icon: 'info',
                confirmButtonColor: '#0dcaf0'
            });
        }
    }

    if (data.error) {
        Swal.fire({
            title: 'Thanh toán thất bại',
            text: data.error,
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    }
});