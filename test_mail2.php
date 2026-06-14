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

// Thử gửi cho 1 email khác
$testEmail = 'thangx122@gmail.com'; 

$result = $mailService->sendInvoiceEmail($testEmail, 'Khách hàng Test', 999, $cartDetails, 100000, 'Test Address', null, 0);

if ($result) {
    echo "Gửi mail thành công đến $testEmail!";
} else {
    echo "Gửi mail thất bại đến $testEmail!";
}
?>
