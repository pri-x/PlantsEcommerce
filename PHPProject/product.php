<?php
require_once 'includes/Product.php';
require_once 'includes/Cart.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$productObj = new Product();
$cart = new Cart();

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $productObj->getProductById($product_id);

if (!$product) {
    header("Location: index.php");
    exit;
}

// Add-to-cart functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    $cart->addToCart($product_id, $quantity);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Plant Store</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    
    <div class="container mt-5">
        <h1 class="text-center mb-4"><?= htmlspecialchars($product['name']) ?></h1>
        
        <div class="row">
   
            <div class="col-md-6 mb-4">
                <img src="images/plants/<?= htmlspecialchars($product['image_url']) ?>" class="img-fluid rounded shadow-sm" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>

          
            <div class="col-md-6">
                <p class="text-muted h4">$<?= number_format($product['price'], 2) ?></p>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <p><strong>Available Stock:</strong> <?= htmlspecialchars($product['stock']) ?></p>

               
                <?php if ($product['stock'] > 0) : ?>
                    <form method="POST" action="product.php?id=<?= $product['product_id'] ?>">
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?= htmlspecialchars($product['stock']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Add to Cart</button>
                    </form>
                <?php else : ?>
                    <div class="alert alert-warning mt-3">This plant is currently out of stock.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
