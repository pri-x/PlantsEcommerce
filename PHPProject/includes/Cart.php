<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Cart
{
    public function addToCart($product_id, $quantity)
    {
        // Initialize cart session if not set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Fetching product details from the database 
        $product = $this->getProductDetails($product_id);

        if ($product) {
            // Ensureing the cart entry for the product ID is an array
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 0
                ];
            } elseif (!is_array($_SESSION['cart'][$product_id])) {
                // Checking if the value is not an array and reset it
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 0
                ];
            }

            // Update quantity for the product in the cart
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        }
    }

    public function removeFromCart($product_id)
    {
        // Remove product from the cart if it exists
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    public function getCartItems()
    {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    public function getCartItemCount()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        return count($_SESSION['cart']);
    }

    public function calculateTotal()
    {
        $total = 0;
        // Calculate the total cost of all products in the cart
        foreach ($this->getCartItems() as $product_id => $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    // Fetch product details 
    private function getProductDetails($product_id)
    {
        // Replace with actual database query to fetch product details by ID
        $productObj = new Product();
        return $productObj->getProductById($product_id);
    }

    public function updateCartItem($product_id, $quantity)
    {
        // Ensure the quantity is updated properly in the cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }
}
