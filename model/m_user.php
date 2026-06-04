<?php
class UserModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function checkLogin($username, $password) {
        // Trong hệ thống thực tế, bạn nên mã hóa password, ví dụ dùng password_verify().
        // Ở đây chúng ta đang dùng text thô để dễ kiểm tra ở giai đoạn đầu.
        $query = "SELECT * FROM `USER` WHERE `UserName` = :username AND `PassWord` = :password LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về false nếu không tìm thấy, ngược lại trả về mảng dữ liệu
    }
}
?>
