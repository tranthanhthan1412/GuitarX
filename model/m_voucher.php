<?php
class VoucherModel {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getAllVouchers() {
        $query = "SELECT * FROM `VOUCHERS` ORDER BY `Vouchers_ID` DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addVoucher($code, $discount_value, $quantity, $expiry_date) {
        $query = "INSERT INTO `VOUCHERS` (`Code`, `discount_value`, `quantity`, `expiry_date`) 
                  VALUES (:code, :value, :qty, :expiry)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':value', $discount_value);
        $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':expiry', $expiry_date);
        return $stmt->execute();
    }

    public function updateVoucher($id, $code, $discount_value, $quantity, $expiry_date) {
        $query = "UPDATE `VOUCHERS` SET `Code` = :code, `discount_value` = :value, 
                  `quantity` = :qty, `expiry_date` = :expiry WHERE `Vouchers_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':value', $discount_value);
        $stmt->bindParam(':qty', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':expiry', $expiry_date);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteVoucher($id) {
        $query = "DELETE FROM `VOUCHERS` WHERE `Vouchers_ID` = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getVoucherByCode($code) {
        $query = "SELECT * FROM `VOUCHERS` WHERE `Code` = :code LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
