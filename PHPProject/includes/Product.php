<?php
require_once 'dbconn.php';

class Product {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAllProducts($filter = null) {
        $query = "SELECT * FROM products";
        if ($filter) {
            $query .= " WHERE name LIKE :filter";
        }
        $stmt = $this->conn->prepare($query);
        if ($filter) {
            $stmt->bindValue(':filter', "%$filter%", PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProduct($name, $description, $price, $stock, $image_url) {
        $stmt = $this->conn->prepare("INSERT INTO products (name, description, price, stock, image_url) 
                                      VALUES (:name, :description, :price, :stock, :image_url)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':image_url', $image_url);
        return $stmt->execute();
    }
}
?>
