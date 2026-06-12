<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require the PHPMailer classes
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

class MailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        // Cấu hình SMTP
        try {
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = SMTP_USER;
            $this->mail->Password   = SMTP_PASS;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            $this->mail->CharSet    = 'UTF-8';
        } catch (Exception $e) {
            error_log("Lỗi cấu hình Mail: {$this->mail->ErrorInfo}");
        }
    }

    public function sendInvoiceEmail($customerEmail, $customerName, $orderId, $cartDetails, $totalAmount, $address, $voucherCode = null, $discountValue = 0) {
        try {
            $this->mail->setFrom('no-reply@guitarx.vn', 'GuitarX - Nhạc Cụ Chính Hãng');
            $this->mail->addAddress($customerEmail, $customerName);

            $this->mail->isHTML(true);
            $this->mail->Subject = "Xác nhận đơn hàng #" . $orderId . " từ GuitarX";

            $itemsHtml = '';
            foreach ($cartDetails as $item) {
                // Nhúng ảnh sản phẩm trực tiếp vào email (Embed Image)
                $imgPath = __DIR__ . '/../view/image/' . $item['Anh'];
                $cid = 'img_' . $item['Ma_SanPham'];
                
                if (file_exists($imgPath) && !empty($item['Anh'])) {
                    $this->mail->addEmbeddedImage($imgPath, $cid);
                    $imgSrc = "cid:$cid";
                } else {
                    $imgSrc = ""; // Nếu không có ảnh thật thì để trống
                }

                $itemsHtml .= "
                <tr>
                    <td style='padding: 15px; border-bottom: 1px solid #eeeeee;'>
                        <img src='{$imgSrc}' alt='{$item['TenSanPham']}' style='width: 80px; height: auto; border-radius: 8px;'/>
                    </td>
                    <td style='padding: 15px; border-bottom: 1px solid #eeeeee;'>
                        <strong style='color: #333; font-size: 16px;'>{$item['TenSanPham']}</strong><br/>
                        <span style='color: #888;'>Số lượng: {$item['SoLuong']}</span>
                    </td>
                    <td style='padding: 15px; border-bottom: 1px solid #eeeeee; text-align: right; font-weight: bold; color: #e63946;'>
                        " . number_format($item['Subtotal'], 0, ',', '.') . "₫
                    </td>
                </tr>";
            }

            // HTML Template Email
            $body = "
            <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px 0;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);'>
                    <!-- Header -->
                    <div style='background-color: #1a1a1a; padding: 30px; text-align: center; border-bottom: 4px solid #e63946;'>
                        <h1 style='color: #ffffff; margin: 0; font-size: 28px; letter-spacing: 2px;'>GuitarX</h1>
                        <p style='color: #a0a0a0; margin: 10px 0 0 0;'>Cảm ơn bạn đã mua sắm cùng chúng tôi!</p>
                    </div>

                    <!-- Body -->
                    <div style='padding: 40px 30px;'>
                        <h2 style='color: #333; font-size: 22px; margin-top: 0;'>Xin chào {$customerName},</h2>
                        <p style='color: #555; line-height: 1.6;'>Đơn hàng <strong>#{$orderId}</strong> của bạn đã được ghi nhận hệ thống. Chúng tôi đang tiến hành xử lý và sẽ giao hàng đến bạn trong thời gian sớm nhất.</p>
                        
                        <div style='background-color: #f9f9f9; padding: 20px; border-radius: 8px; margin: 30px 0;'>
                            <h3 style='margin-top: 0; color: #333; font-size: 16px; text-transform: uppercase;'>Thông tin giao hàng</h3>
                            <p style='color: #555; margin: 5px 0;'><strong>Người nhận:</strong> {$customerName}</p>
                            <p style='color: #555; margin: 5px 0;'><strong>Địa chỉ:</strong> {$address}</p>
                        </div>

                        <h3 style='color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px;'>Chi tiết đơn hàng</h3>
                        <table style='width: 100%; border-collapse: collapse; margin-bottom: 30px;'>
                            {$itemsHtml}
                        </table>

                        <div style='text-align: right; background-color: #f9f9f9; padding: 20px; border-radius: 8px;'>
                            <p style='color: #555; margin: 5px 0;'>Phí giao hàng: <strong>Miễn phí</strong></p>";
                            
            if ($discountValue > 0) {
                $finalTotal = max(0, $totalAmount - $discountValue);
                $body .= "  <p style='color: #555; margin: 5px 0;'>Tạm tính: <strong>" . number_format($totalAmount, 0, ',', '.') . "₫</strong></p>
                            <p style='color: #555; margin: 5px 0;'>Giảm giá (Mã {$voucherCode}): <strong style='color: #e63946;'>-" . number_format($discountValue, 0, ',', '.') . "₫</strong></p>
                            <h2 style='color: #e63946; margin: 10px 0 0 0; font-size: 24px;'>Tổng thanh toán: " . number_format($finalTotal, 0, ',', '.') . "₫</h2>";
            } else {
                $body .= "  <h2 style='color: #e63946; margin: 10px 0 0 0; font-size: 24px;'>Tổng cộng: " . number_format($totalAmount, 0, ',', '.') . "₫</h2>";
            }

            $body .= "
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style='background-color: #f4f4f4; padding: 20px; text-align: center; color: #888; font-size: 12px;'>
                        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ hotline: 1900 6717 hoặc email: info@guitarx.vn</p>
                        <p>© 2026 GuitarX. All rights reserved.</p>
                    </div>
                </div>
            </div>
            ";

            $this->mail->Body = $body;
            $this->mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Lỗi gửi email hóa đơn: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}
?>
