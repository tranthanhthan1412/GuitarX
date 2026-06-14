<?php
class ChatModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Lấy tin nhắn giữa 1 User và Admin
    public function getMessages($userId) {
        $query = "SELECT * FROM `tinnhan` WHERE `User_ID` = :userId ORDER BY `Ma_TinNhan` ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Gửi tin nhắn mới
    public function sendMessage($userId, $content, $isAdminReply = 0) {
        $query = "INSERT INTO `tinnhan` (`User_ID`, `NoiDung`, `Is_Admin_Reply`, `ThoiGian`, `TrangThaiDoc`) 
                  VALUES (:userId, :content, :isAdminReply, NOW(), 0)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':isAdminReply', $isAdminReply, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Lấy danh sách các User đã từng nhắn tin (Dành cho Admin)
    public function getChatUsers() {
        // Cần JOIN với bảng NguoiDung để lấy tên
        $query = "
            SELECT u.Ma_NguoiDung, u.TenNguoiDung, MAX(t.ThoiGian) as LastMessageTime,
                   (SELECT NoiDung FROM tinnhan WHERE User_ID = u.Ma_NguoiDung ORDER BY ThoiGian DESC LIMIT 1) as LastMessage,
                   (SELECT SUM(CASE WHEN TrangThaiDoc = 0 AND Is_Admin_Reply = 0 THEN 1 ELSE 0 END) FROM tinnhan WHERE User_ID = u.Ma_NguoiDung) as UnreadCount
            FROM `nguoidung` u
            JOIN `tinnhan` t ON u.Ma_NguoiDung = t.User_ID
            GROUP BY u.Ma_NguoiDung, u.TenNguoiDung
            ORDER BY LastMessageTime DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đánh dấu tin nhắn đã đọc
    public function markAsRead($userId, $isAdminReading = true) {
        // Nếu Admin đọc -> Cập nhật tin của User (Is_Admin_Reply = 0) thành Đã đọc
        // Nếu User đọc -> Cập nhật tin của Admin (Is_Admin_Reply = 1) thành Đã đọc
        $targetReplyType = $isAdminReading ? 0 : 1;
        $query = "UPDATE `tinnhan` SET `TrangThaiDoc` = 1 WHERE `User_ID` = :userId AND `Is_Admin_Reply` = :targetReplyType AND `TrangThaiDoc` = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':targetReplyType', $targetReplyType, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Đếm số lượng tin nhắn chưa đọc của một User (dành cho icon ngoài Front-end)
    public function countUnreadForUser($userId) {
        $query = "SELECT COUNT(*) as unread FROM `tinnhan` WHERE `User_ID` = :userId AND `Is_Admin_Reply` = 1 AND `TrangThaiDoc` = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['unread'] : 0;
    }
}
?>
