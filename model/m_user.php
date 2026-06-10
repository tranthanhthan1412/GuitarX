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
    // --- ADMIN METHODS ---
    public function getAllUsers() {
        // 1. Lấy toàn bộ user từ database ra trước
        $sql = "SELECT * FROM `USER` ORDER BY `User_ID` DESC";
        $stmt = $this->db->prepare($sql); 
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Duyệt qua từng user, dùng chính hàm getCustomerRank để tính toán Rank thực tế
        foreach ($users as &$user) {
            // Gọi hàm getCustomerRank có sẵn ở phía dưới của class để lấy Rank theo số tiền tích lũy
            $rankInfo = $this->getCustomerRank($user['User_ID']);
            
            // Ép tên Rank lấy được vào mảng để file view admin/quanlyuser.php đọc được
            $user['RankName'] = $rankInfo['name'];
        }

        return $users;
    }

    public function changeUserRole($userId, $newRole) {
        $query = "UPDATE `USER` SET `Role` = :role WHERE `User_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":role", $newRole);
        $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // --- KHÁCH HÀNG METHODS: TỰ ĐỘNG TÍNH RANK & % GIẢM GIÁ ---
    public function getCustomerRank($userId) {
        $query = "SELECT SUM(od.`Total`) as total 
                  FROM `orders` o 
                  INNER JOIN `order_detail` od ON o.`Order_ID` = od.`Order_ID`
                  WHERE o.`User_ID` = :id AND (o.`Status` = 'Completed' OR o.`Status` = 'Pending')";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $totalSpent = $result['total'] ?? 0; // Nếu chưa mua đơn nào thành công thì mặc định bằng 0

        // 2. Logic phân hạng dựa trên số tiền chi tiêu của khách
        if ($totalSpent >= 30000000) { // Từ 30 triệu trở lên
            return [
                'name' => 'Kim Cương',
                'class' => 'bg-info text-dark fw-bold',
                'discount' => 10 // Giảm 10%
            ];
        } elseif ($totalSpent >= 15000000) { // Từ 15 triệu đến dưới 30 triệu
            return [
                'name' => 'Vàng',
                'class' => 'bg-warning text-dark fw-bold',
                'discount' => 5 // Giảm 5%
            ];
        } elseif ($totalSpent >= 5000000) { // Từ 5 triệu đến dưới 15 triệu
            return [
                'name' => 'Bạc',
                'class' => 'bg-secondary text-white',
                'discount' => 2 // Giảm 2%
            ];
        } else { // Dưới 5 triệu
            return [
                'name' => 'Thành viên mới',
                'class' => 'bg-light text-muted border',
                'discount' => 0 // Không giảm
            ];
        }
    }
}
?>