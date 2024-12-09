<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | E-Commerce</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/custom.css">
</head>

<body>
 
    <nav class="navbar navbar-expand-lg bg-success">
        <div class="container">
            <a class="navbar-brand text-white font-weight-bold" href="index.php">E-Commerce</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link text-white font-weight-bold" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white font-weight-bold" href="cart.php">Cart</a></li>

                   
                    <?php if (!$isLoggedIn) : ?>
                        <li class="nav-item"><a class="nav-link text-white font-weight-bold" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link text-white font-weight-bold" href="register.php">Register</a></li>
                    <?php else : ?>
                        
                        <li class="nav-item"><a class="nav-link text-white font-weight-bold" href="logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

</body>

</html>