<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if cart exists in the session
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
} else {
    $cartItems = [];
    echo "Your cart is empty.";
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: register.php?redirect=checkout");
    exit;
}

// If the cart is empty, redirect to cart page
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

require_once 'includes/Product.php';

$productObj = new Product();
$total = 0;

// Loop through cart items to get product details
foreach ($cartItems as $product_id => $item) {
    $product = $productObj->getProductById($product_id);
    if ($product) {
        $item['name'] = $product['name'];
        $item['price'] = $product['price'];
        $item['total'] = $item['quantity'] * $product['price'];
        $total += $item['total'];
        $cartItems[$product_id] = $item;
    }
}

// Handle form submission (to capture shipping details)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zipcode = $_POST['zipcode'];
    $email = $_POST['email'];

    // Store the order in the session or database (optional)
    $_SESSION['order'] = [
        'name' => $name,
        'address' => $address,
        'city' => $city,
        'zipcode' => $zipcode,
        'email' => $email,
        'cart' => $cartItems,
        'total' => $total
    ];

    // Generate PDF invoice
    generateInvoice($_SESSION['order']);
}

// Function to generate PDF invoice
function generateInvoice($order)
{
    require_once('./fpdfg/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();

    // Header Section
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'ECommers', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, '123 Business Street, City, Country', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Phone: (123) 456-7890 | Email: support@ecommers.com', 0, 1, 'C');
    $pdf->Ln(10);

    // Customer Information
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Invoice', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Name: ' . $order['name'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Address: ' . $order['address'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'City: ' . $order['city'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Zipcode: ' . $order['zipcode'], 0, 1, 'L');
    $pdf->Cell(0, 10, 'Email: ' . $order['email'], 0, 1, 'L');
    $pdf->Ln(10);

    // Cart Items
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80, 10, 'Product', 1);
    $pdf->Cell(30, 10, 'Quantity', 1);
    $pdf->Cell(30, 10, 'Price', 1);
    $pdf->Cell(30, 10, 'Total', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    foreach ($order['cart'] as $item) {
        if (isset($item['name'], $item['quantity'], $item['price'])) {
            $pdf->Cell(80, 10, $item['name'], 1);
            $pdf->Cell(30, 10, $item['quantity'], 1, 0, 'C');
            $pdf->Cell(30, 10, '$' . number_format($item['price'], 2), 1, 0, 'R');
            $pdf->Cell(30, 10, '$' . number_format($item['price'] * $item['quantity'], 2), 1, 0, 'R');
            $pdf->Ln();
        }
    }

    // Total
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(140, 10, 'Total:', 1);
    $pdf->Cell(30, 10, '$' . number_format($order['total'], 2), 1, 0, 'R');
    $pdf->Ln(20);

    // Footer
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Thank you for your purchase!', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Please contact us for any questions regarding this invoice.', 0, 1, 'C');

    $pdf->Output('I', 'invoice.pdf');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="css/bootstrap.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="text-center my-4">Checkout</h1>
        <div class="row">
            <div class="col-md-6">
                <h3>Your Cart</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>$<?php echo number_format($item['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3 class="text-right">Total: $<?php echo number_format($total, 2); ?></h3>
            </div>
            <div class="col-md-6">
                <h3>Shipping Information</h3>
                <form action="checkout.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" name="address" id="address" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" class="form-control" name="city" id="city" required>
                    </div>
                    <div class="form-group">
                        <label for="zipcode">Zipcode</label>
                        <input type="text" class="form-control" name="zipcode" id="zipcode" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <button type="submit" class="btn btn-success">Generate Invoice</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
