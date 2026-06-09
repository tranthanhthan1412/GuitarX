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

    // Kiểm tra xem tên đăng nhập đã tồn tại chưa
    public function checkUserExists($username) {
        $query = "SELECT COUNT(*) FROM `USER` WHERE `UserName` = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Đăng ký tài khoản mới
    public function register($username, $password, $email, $phone) {
        if ($this->checkUserExists($username)) {
            return false; // Tên đăng nhập đã tồn tại
        }
        
        $query = "INSERT INTO `USER` (`UserName`, `PassWord`, `Email`, `PhoneNumber`, `Role`) VALUES (:username, :password, :email, :phone, 'customer')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        
        return $stmt->execute();
    }

    // --- ADMIN METHODS ---
    public function getAllUsers() {
        $query = "SELECT `User_ID`, `UserName`, `Email`, `PhoneNumber`, `Role`, `Create_At` FROM `USER` ORDER BY `User_ID` DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function changeUserRole($userId, $newRole) {
        $query = "UPDATE `USER` SET `Role` = :role WHERE `User_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":role", $newRole);
        $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
