<?php

class OrderModel
{
  private $conn;

  public function __construct()
  {
    $db = new Database();
    $this->conn = $db->getConnection();
  }

  public function getOrderDetails($order_id)
  {
    try {
      $stmt = $this->conn->prepare("SELECT transaction_number, total_amount FROM orders WHERE id = ?");
      $stmt->bind_param("i", $order_id);
      $stmt->execute();
      $result = $stmt->get_result();

      return $result->fetch_assoc();
    } catch (Exception $e) {
      return ['error' => 'Error fetching order details: ' . $e->getMessage()];
    }
  }

  public function getUserOrders($user_id)
  {
    try {
      // Step 1: Fetch orders for the user
      $stmt = $this->conn->prepare(
        "SELECT id, transaction_number, total_amount, created_at 
               FROM orders 
               WHERE user_id = ? 
               ORDER BY created_at DESC"
      );
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      $orders = [];
      while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
      }

      // Step 2: Fetch products for each order
      foreach ($orders as &$order) {
        $order_id = $order['id'];

        // Fetch products for the current order including the image
        $productStmt = $this->conn->prepare(
          "SELECT 
                      oi.product_id, 
                      oi.quantity, 
                      oi.price, 
                      p.name AS product_name, 
                      p.image AS product_image
                   FROM order_items oi
                   JOIN products p ON oi.product_id = p.id
                   WHERE oi.order_id = ?"
        );
        $productStmt->bind_param("i", $order_id);
        $productStmt->execute();
        $productResult = $productStmt->get_result();

        $products = [];
        while ($product = $productResult->fetch_assoc()) {
          $products[] = $product;
        }

        // Add products to the order
        $order['products'] = $products;
      }

      return $orders;
    } catch (Exception $e) {
      return ['error' => 'Error fetching orders: ' . $e->getMessage()];
    }
  }
}
