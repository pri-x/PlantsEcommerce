  <?php
    class Database
    {
        private $host = 'localhost';
        private $db_name = 'PlantsEcommerce';
        private $username = 'root';
        private $password = '';
        public $conn;

        public function connect()
        {
            $this->conn = null;
            try {
                $this->conn = new PDO("mysql:host=$this->host", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Create the database if it doesn't exist
                $this->conn->exec("CREATE DATABASE IF NOT EXISTS $this->db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                // Select the database
                $this->conn->exec("USE $this->db_name");

                // Create the tables
                $this->createTables();

                // Populate initial data
                $this->populateData();
            } catch (PDOException $e) {
                echo "Connection error: " . $e->getMessage();
            }
            return $this->conn;
        }

        private function createTables()
        {
            $queries = [
                // Users table
                "CREATE TABLE IF NOT EXISTS users (
                        user_id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        email VARCHAR(100) NOT NULL UNIQUE,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )",

                // Products table
                "CREATE TABLE IF NOT EXISTS products (
                        product_id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL UNIQUE,
                        description TEXT,
                        price DECIMAL(10, 2) NOT NULL,
                        stock INT NOT NULL,
                        image_url VARCHAR(255),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )",

                // Orders table
                "CREATE TABLE IF NOT EXISTS orders (
                        order_id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        total_amount DECIMAL(10, 2) NOT NULL,
                        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        UNIQUE(user_id, total_amount),
                        FOREIGN KEY (user_id) REFERENCES users(user_id)
                    )",

                // Order details table
                "CREATE TABLE IF NOT EXISTS order_details (
                        detail_id INT AUTO_INCREMENT PRIMARY KEY,
                        order_id INT NOT NULL,
                        product_id INT NOT NULL,
                        quantity INT NOT NULL,
                        price DECIMAL(10, 2) NOT NULL,
                        UNIQUE(order_id, product_id),
                        FOREIGN KEY (order_id) REFERENCES orders(order_id),
                        FOREIGN KEY (product_id) REFERENCES products(product_id)
                    )"
            ];

            foreach ($queries as $query) {
                $this->conn->exec($query);
            }
        }

        private function populateData()
        {
            // Insert products real data
            $productsData = [
                ['Monstera Deliciosa', 'A tropical plant with split leaves, perfect for indoor spaces.', 25.99, 50, 'Monstera Deliciosa.jpg'],
                ['Snake Plant', 'Low-maintenance plant with striking vertical leaves.', 15.49, 30, 'Snake Plant.jpg'],
                ['Peace Lily', 'Beautiful flowering plant known for purifying indoor air.', 20.00, 40, 'Peace Lily.jpg'],
                ['Fiddle Leaf Fig', 'Popular houseplant with large, violin-shaped leaves.', 45.00, 20, 'Fiddle Leaf Fig.jpg'],
                ['Spider Plant', 'Great for beginners, known for its arching leaves.', 10.99, 60, 'Spider Plant.jpg'],
                ['Aloe Vera', 'A succulent plant known for its healing properties.', 12.00, 35, 'Aloe Vera.jpg'],
                ['Pothos', 'A versatile, fast-growing plant thats easy to maintain.', 8.99, 75, 'Pothos.jpg'],
                ['Rubber Plant', 'A tall, attractive plant with glossy leaves.', 30.00, 25, 'Rubber Plant.jpg'],
                ['Bamboo Palm', 'An indoor palm that purifies the air and adds tropical charm.', 18.00, 45, 'Bamboo Palm.jpg'],
                ['Cactus Mix', 'An assortment of easy-to-care-for desert plants.', 22.99, 50, 'Cactus Mix.jpg'],
                ['ZZ Plant', 'A hardy, drought-tolerant plant perfect for beginners.', 19.99, 40, 'ZZ Plant.jpg'],
                ['Calathea', 'Known for its striking patterned leaves and vibrant colors.', 16.99, 30, 'Calathea.jpg'],
                ['Bird of Paradise', 'A statement plant with large, tropical leaves.', 55.00, 15, 'Bird of Paradise.jpg'],
                ['Succulent Trio', 'A trio of cute, small succulents in a single pack.', 12.99, 65, 'Succulent Trio.jpg'],
                ['Boston Fern', 'A lush, elegant plant that thrives in humid conditions.', 14.50, 20, 'Boston Fern.jpg']
            ];

            foreach ($productsData as $product) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
                $stmt->execute([$product[0]]);
                if ($stmt->fetchColumn() == 0) { // If product doesn't exist
                    $insertStmt = $this->conn->prepare("INSERT INTO products (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)");
                    $insertStmt->execute($product);
                }
            }

            // Insert users
            $usersData = [
                ['john_doe', password_hash('password123', PASSWORD_DEFAULT), 'john.doe@example.com'],
                ['jane_doe', password_hash('password456', PASSWORD_DEFAULT), 'jane.doe@example.com'],
                ['alice_smith', password_hash('password789', PASSWORD_DEFAULT), 'alice.smith@example.com']
            ];

            foreach ($usersData as $user) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
                $stmt->execute([$user[0]]);
                if ($stmt->fetchColumn() == 0) { // If user doesn't exist
                    $insertStmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
                    $insertStmt->execute($user);
                }
            }

            // Insert orders dummy data
            $ordersData = [
                [1, 51.98],
                [2, 30.98],
                [3, 20.00]
            ];

            foreach ($ordersData as $order) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND total_amount = ?");
                $stmt->execute($order);
                if ($stmt->fetchColumn() == 0) { // If order doesn't exist
                    $insertStmt = $this->conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
                    $insertStmt->execute($order);
                }
            }

            // Insert order details dummy data
            $orderDetailsData = [
                [1, 1, 2, 25.99],
                [2, 2, 1, 15.49],
                [2, 5, 1, 10.99],
                [3, 3, 1, 20.00]
            ];

            foreach ($orderDetailsData as $detail) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM order_details WHERE order_id = ? AND product_id = ?");
                $stmt->execute([$detail[0], $detail[1]]);
                if ($stmt->fetchColumn() == 0) { // If detail doesn't exist
                    $insertStmt = $this->conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $insertStmt->execute($detail);
                }
            }
        }
    }
