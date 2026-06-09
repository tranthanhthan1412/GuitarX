<?php
session_start();
require_once 'model/m_mail.php';

$mailService = new MailService();

$customerEmail = 'tranthanhthang333@gmail.com'; // test sending to self
$customerName = 'Test User';
$orderId = 999;
$cartDetails = [
    [
        'Image' => 'acoustic.jpg',
        'Product_ID' => 1,
        'ProductName' => 'Test Guitar',
        'Quantity' => 1,
        'Subtotal' => 1500000
    ]
];
$totalAmount = 1500000;
$address = 'Test Address, TP.HCM';

echo "Sending email to: $customerEmail ...<br>";
$result = $mailService->sendInvoiceEmail($customerEmail, $customerName, $orderId, $cartDetails, $totalAmount, $address);

if ($result) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email. Check error logs.";
}
?>
