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
      $stmt = $this->conn->prepare("SELECT id, transaction_number, total_amount, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      $orders = [];
      while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
      }
      return $orders;
    } catch (Exception $e) {
      return ['error' => 'Error fetching orders: ' . $e->getMessage()];
    }
  }
}
