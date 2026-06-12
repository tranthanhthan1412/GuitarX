<?php
class VoucherModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getAllVouchers() {
        $query = "SELECT * FROM `MaGiamGia` ORDER BY `Ma_MaGiamGia` DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addVoucher($code, $GiaTriGiam, $quantity, $NgayHetHan) {
        $query = "INSERT INTO `MaGiamGia` (`Ma`, `GiaTriGiam`, `SoLuong`, `NgayHetHan`) 
                  VALUES (:code, :value, :qty, :expiry)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':value', $GiaTriGiam);
        $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':expiry', $NgayHetHan);
        return $stmt->execute();
    }

    public function updateVoucher($id, $code, $GiaTriGiam, $quantity, $NgayHetHan) {
        $query = "UPDATE `MaGiamGia` SET `Ma` = :code, `GiaTriGiam` = :value, 
                  `SoLuong` = :qty, `NgayHetHan` = :expiry WHERE `Ma_MaGiamGia` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':value', $GiaTriGiam);
        $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':expiry', $NgayHetHan);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteVoucher($id) {
        $query = "DELETE FROM `MaGiamGia` WHERE `Ma_MaGiamGia` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getVoucherByCode($code) {
        $query = "SELECT * FROM `MaGiamGia` WHERE `Ma` = :code LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
