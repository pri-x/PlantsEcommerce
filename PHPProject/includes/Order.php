<?php
require_once 'dbconn.php';
require_once '../fpdfg/fpdf.php';

class Order
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function createOrder($user_id, $cartItems, $totalAmount)
    {
        $this->conn->beginTransaction();

        try {
            // Insert into orders table
            $stmt = $this->conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (:user_id, :total_amount)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':total_amount', $totalAmount);
            $stmt->execute();
            $order_id = $this->conn->lastInsertId();

            // Insert into order_details table
            foreach ($cartItems as $product_id => $quantity) {
                $stmt = $this->conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) 
                                              VALUES (:order_id, :product_id, :quantity, 
                                                      (SELECT price FROM products WHERE product_id = :product_id))");
                $stmt->bindParam(':order_id', $order_id);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':quantity', $quantity);
                $stmt->execute();
            }

            $this->conn->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function generateInvoice($order_id)
    {
        $stmt = $this->conn->prepare("SELECT o.order_id, o.total_amount, o.order_date, u.username, u.email, 
                                      p.name, od.quantity, od.price
                                      FROM orders o
                                      JOIN users u ON o.user_id = u.user_id
                                      JOIN order_details od ON o.order_id = od.order_id
                                      JOIN products p ON od.product_id = p.product_id
                                      WHERE o.order_id = :order_id");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add Order Info
        $pdf->Cell(0, 10, "Invoice for Order #$order_id", 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);

        foreach ($orderDetails as $detail) {
            $pdf->Cell(0, 10, "{$detail['name']} - {$detail['quantity']} x {$detail['price']}", 0, 1);
        }

        $pdf->Cell(0, 10, "Total: {$orderDetails[0]['total_amount']}", 0, 1, 'R');
        $pdf->Output('I', "invoice_$order_id.pdf");
    }
}
