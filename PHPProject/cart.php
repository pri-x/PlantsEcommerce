<?php
require_once 'includes/Product.php';
require_once 'includes/Cart.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$productObj = new Product();
$cart = new Cart();

$cartItems = $cart->getCartItems();
$products = [];
$total = 0;

// Fetch product details for items in the cart
if (!empty($cartItems)) {
    foreach ($cartItems as $product_id => $item) {
        $product = $productObj->getProductById($product_id);
        if (is_array($product)) {
            $price = isset($product['price']) ? (float)$product['price'] : 0;
            $quantity = (int)$item['quantity'];  // Get the quantity from the updated session

            $product['quantity'] = $quantity;
            $products[$product_id] = $product;

            // Update subtotal calculation after the quantity is updated
            $total += $price * $quantity;
        }
    }
}


// Handle remove from cart
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $cart->removeFromCart(intval($_GET['remove']));
    header("Location: cart.php");
    exit;
}

// Handle update cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $quantity = intval($quantity);

        // Ensure the product exists and the quantity is valid
        if ($quantity > 0) {
            if (isset($cartItems[$product_id])) {
                // Update the quantity in the cart
                $cart->updateCartItem($product_id, $quantity);
            }
        } else {
            // Remove the product if the quantity is set to 0 or less
            $cart->removeFromCart($product_id);
        }
    }

    // Redirect to prevent resubmission of the form
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Plant Store</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Your Shopping Cart</h1>

        <?php if (empty($products)) : ?>
            <div class="alert alert-warning text-center">Your cart is empty. <a href="index.php">Continue shopping</a>.</div>
        <?php else : ?>
            <div class="table-responsive">
                <form method="POST" action="cart.php">
                    <table class="table table-bordered text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>Plant</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product_id => $product) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td>$<?= number_format($product['price'], 2) ?></td>
                                    <td>
                                        <input type="number" name="quantities[<?= $product_id ?>]" value="<?= $product['quantity'] ?>" min="1" max="<?= $product['stock'] ?>" class="form-control" style="width: 80px; margin: auto;">
                                    </td>
                                    <td>$<?= number_format($product['price'] * $product['quantity'], 2) ?></td>
                                    <td>
                                        <a href="cart.php?remove=<?= $product_id ?>" class="btn btn-danger btn-sm">Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Update Cart</button>
                        <h3 class="text-success">Total: $<?= number_format($total, 2) ?></h3>
                        <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <footer class="text-center py-3 bg-light mt-5">
        <p>&copy; <?= date('Y') ?> Plant Store. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>