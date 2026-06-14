<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/model/m_mail.php';

$mailService = new MailService();

$cartDetails = [
    [
        'Ma_SanPham' => 1,
        'TenSanPham' => 'Test Product',
        'SoLuong' => 1,
        'Subtotal' => 100000,
        'Anh' => ''
    ]
];

$result = $mailService->sendInvoiceEmail('thangx122@gmail.com', 'Test User', 999, $cartDetails, 100000, 'Test Address', null, 0);

if ($result) {
    echo "Gửi mail thành công!";
} else {
    echo "Gửi mail thất bại!";
}
?>
