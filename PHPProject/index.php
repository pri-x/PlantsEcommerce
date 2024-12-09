<?php
session_start();


require_once 'includes/Product.php';
require_once 'includes/Cart.php';

$productObj = new Product();
$cart = new Cart();

$filter = isset($_GET['search']) ? $_GET['search'] : null;
$products = $productObj->getAllProducts($filter);

?>
<?php include 'includes/header.php'; ?>


<div class="jumbotron">
    <h1>Welcome to Plant Store</h1>
    <p>Your one-stop shop for the freshest and most beautiful plants.</p>
    <a href="#featured-products" class="btn btn-primary">Shop Now</a>
</div>


<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a class="navbar-brand" href="index.php">Plant Store</a>
        <form class="form-inline" method="GET" action="index.php">
            <input class="form-control" type="search" placeholder="Search Plants" name="search" value="<?= htmlspecialchars($filter) ?>">
            <button class="btn btn-outline-success ml-2" type="submit">Search</button>
        </form>
        <a class="btn btn-outline-primary" href="cart.php">Cart (<?= $cart->getCartItemCount() ?>)</a>
    </div>
</div>


<div class="container" id="featured-products">
    <h2>Featured Products</h2>
    <div class="row">
        <?php foreach ($products as $product) : ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="images/plants/<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text">$<?= number_format($product['price'], 2) ?></p>
                        <a href="product.php?id=<?= $product['product_id'] ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<?php include 'includes/footer.php'; ?>