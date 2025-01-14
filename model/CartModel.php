<?php

require_once __DIR__ . '/Database.php';  // Use __DIR__ for reliable path resolution

class CartModel
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }


    // Get all products
    public function getProducts($page, $itemsPerPage, $search = '', $sortBy = null, $order = null)
    {

        $orderClause = '';

        // Apply sorting if the sort parameters are provided
        if ($sortBy && $order) {
            $validSortBy = ['price', 'name'];
            $validOrder = ['asc', 'desc'];

            // Validate and sanitize sort parameters
            $sortBy = in_array($sortBy, $validSortBy) ? $sortBy : 'price';
            $order = in_array($order, $validOrder) ? $order : 'asc';

            // Add the ORDER BY clause to the query
            $orderClause = " ORDER BY " . $sortBy . " " . $order;
        }

        // Calculate pagination offset
        $offset = ($page - 1) * $itemsPerPage;

        // Construct the SQL query with optional search and pagination
        $searchSql = "";
        if ($search) {
            $searchSql = " AND (name LIKE ? OR description LIKE ?)";
        }

        $sql = "SELECT id, name, description, price, image, quantity
                FROM products
                WHERE 1=1" . $searchSql . $orderClause . "
                LIMIT ?, ?";

        $stmt = $this->conn->prepare($sql);

        if ($search) {
            $searchTerm = '%' . $search . '%';
            $stmt->bind_param("ssii", $searchTerm, $searchTerm, $offset, $itemsPerPage);
        } else {
            $stmt->bind_param("ii", $offset, $itemsPerPage);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $products = $result->fetch_all(MYSQLI_ASSOC);

        // Get the total count of products for pagination
        $countSql = "SELECT COUNT(*) FROM products WHERE 1=1" . $searchSql;
        $countStmt = $this->conn->prepare($countSql);
        if ($search) {
            $countStmt->bind_param("ss", $searchTerm, $searchTerm);
        }
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalProducts = $countResult->fetch_row()[0];

        return [
            'products' => $products,
            'totalProducts' => $totalProducts
        ];
    }


    public function getProductById($product_id)
    {
        // Query to get the selected product
        $productSql = "SELECT id, name, description, price, image, quantity FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($productSql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            return null; // Return null if the product is not found
        }

        // Query to get the next 5 products
        $nextProductsSql = "SELECT id, name, price, image, quantity 
                        FROM products 
                        WHERE id > ? 
                        ORDER BY id ASC 
                        LIMIT 5";
        $stmtNext = $this->conn->prepare($nextProductsSql);
        if (!$stmtNext) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        $stmtNext->bind_param("i", $product_id);
        $stmtNext->execute();
        $nextProductsResult = $stmtNext->get_result();
        $nextProducts = $nextProductsResult->fetch_all(MYSQLI_ASSOC);

        return [
            'product' => $product,
            'nextProducts' => $nextProducts,
        ];
    }



    // Get cart items for a user
    public function getCartItems($user_id)
    {
        $sql = "SELECT 
                    c.product_id, 
                    c.quantity, 
                    p.name, 
                    p.price, 
                    p.image, 
                    p.quantity AS stock 
                FROM cart c
                INNER JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCartItemCount($user_id)
    {
        $sql = "SELECT SUM(quantity) AS total_items 
            FROM cart 
            WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ['error' => 'Failed to prepare statement'];
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? (int) $row['total_items'] : 0; // Return 0 if no items
    }

    // Add item to cart
    public function addToCart($user_id, $product_id, $quantity)
    {
        $sql = "INSERT INTO cart (user_id, product_id, quantity) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE quantity = quantity + ?";

        try {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }
            $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
            $result = $stmt->execute();
            if (!$result) {
                throw new Exception("Failed to execute statement: " . $stmt->error);
            }
            return $result;
        } catch (Exception $e) {
            error_log('CartModel error: ' . $e->getMessage());
            throw $e; // Re-throw the exception to be caught in the controller
        }
    }

    // Update cart quantity
    public function updateCartQuantity($user_id, $product_id, $quantity)
    {
        $sql = "UPDATE cart SET quantity = ? 
                WHERE user_id = ? AND product_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        return $stmt->execute();
    }

    // Remove item from cart
    public function removeFromCart($user_id, $product_id)
    {
        $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ii", $user_id, $product_id);
        return $stmt->execute();
    }

    public function getCartTotal($userId)
    {
        $stmt = $this->conn->prepare("SELECT SUM(p.price * c.quantity) AS total 
                                      FROM cart c 
                                      JOIN products p ON c.product_id = p.id 
                                      WHERE c.user_id = ?");
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0; // Return 0 if total is NULL
    }

    public function isCartEmpty($userId)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM cart WHERE user_id = ?");
        if (!$stmt) {
            return true; // Assume empty if there's an error
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] == 0;
    }

    public function getProductsForMerchant($merchant_id)
    {
        $sql = "SELECT * FROM products WHERE merchant_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("i", $merchant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        return $products;
    }
    public function getCartSummary($user_id)
    {
        $sql = "SELECT p.name, p.price, c.quantity, (p.price * c.quantity) AS subtotal
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [
                'items' => [],
                'total' => 0,
            ];
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        $total = 0;

        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
            $total += $row['subtotal']; // Calculate total price
        }

        return [
            'items' => $items,
            'total' => $total,
        ];
    }



    public function checkout($user_id)
    {
        $this->conn->begin_transaction(); // Start a transaction
        try {
            // Fetch cart items
            $cartItems = $this->getCartItems($user_id);
            if (empty($cartItems)) {
                throw new Exception("Cart is empty. Cannot proceed with checkout.");
            }

            // Calculate total price
            $total = $this->getCartTotal($user_id);

            // Generate a unique transaction number
            $transactionNumber = 'SNS-TN-' . strtoupper(uniqid($user_id . '-'));

            // Create an order record
            $orderSql = "INSERT INTO orders (user_id, total_amount, transaction_number, created_at) VALUES (?, ?, ?, NOW())";
            $orderStmt = $this->conn->prepare($orderSql);
            if (!$orderStmt) {
                throw new Exception("Failed to prepare order statement: " . $this->conn->error);
            }
            $orderStmt->bind_param("ids", $user_id, $total, $transactionNumber);
            $orderStmt->execute();
            $order_id = $this->conn->insert_id; // Get the newly created order ID

            // Add cart items to order_items table and update product quantity
            $orderItemSql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $updateQuantitySql = "UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?"; // Ensure quantity doesn't go below 0
            $orderItemStmt = $this->conn->prepare($orderItemSql);
            $updateQuantityStmt = $this->conn->prepare($updateQuantitySql);

            if (!$orderItemStmt || !$updateQuantityStmt) {
                throw new Exception("Failed to prepare statements: " . $this->conn->error);
            }

            foreach ($cartItems as $item) {
                // Add item to order_items table
                $orderItemStmt->bind_param(
                    "iiid",
                    $order_id,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                );
                $orderItemStmt->execute();

                // Update product quantity
                $updateQuantityStmt->bind_param(
                    "iii",
                    $item['quantity'],
                    $item['product_id'],
                    $item['quantity']
                );
                $updateQuantityStmt->execute();

                if ($updateQuantityStmt->affected_rows === 0) {
                    throw new Exception("Insufficient stock for product ID: " . $item['product_id']);
                }
            }

            // Clear the user's cart
            $clearCartSql = "DELETE FROM cart WHERE user_id = ?";
            $clearCartStmt = $this->conn->prepare($clearCartSql);
            if (!$clearCartStmt) {
                throw new Exception("Failed to prepare clear cart statement: " . $this->conn->error);
            }
            $clearCartStmt->bind_param("i", $user_id);
            $clearCartStmt->execute();

            $this->conn->commit(); // Commit transaction

            header("Location: /order/confirmation?order_id=" . $order_id);

            return [
                'success' => true,
                'message' => 'Checkout successful',
                'order_id' => $order_id,
                'transaction_number' => $transactionNumber,
            ];
        } catch (Exception $e) {
            $this->conn->rollback(); // Rollback transaction on error
            header("Location: /cart?error=" . urlencode($e->getMessage()));

            return [
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage(),
            ];
        }
    }
}
