<?php
require 'model/database.php';
$db = new Database();
$conn = $db->getConnection();

// Cập nhật dữ liệu cũ để test
$conn->exec("UPDATE `PRODUCTS` SET `DiscountPercent` = 15 WHERE `Product_ID` = 1");
$conn->exec("UPDATE `PRODUCTS` SET `DiscountPercent` = 10 WHERE `Product_ID` = 3");
$conn->exec("UPDATE `PRODUCTS` SET `DiscountPercent` = 20 WHERE `Product_ID` = 7");
$conn->exec("UPDATE `PRODUCTS` SET `DiscountPercent` = 10 WHERE `Product_ID` = 9");
$conn->exec("UPDATE `PRODUCTS` SET `DiscountPercent` = 25 WHERE `Product_ID` = 11");
$conn->exec("UPDATE `PRODUCTS` SET `DiscountPercent` = 15 WHERE `Product_ID` = 13");
$conn->exec("UPDATE `PRODUCTS` SET `DiscountPercent` = 10 WHERE `Product_ID` = 15");

echo "DB updated successfully.";
?>
