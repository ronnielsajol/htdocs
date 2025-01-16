<?php

require_once __DIR__ . '/Database.php';

class UserModel
{
  private $conn;

  public function __construct()
  {
    $database = new Database();
    $this->conn = $database->getConnection();
  }

  public function getOrders()
  {
    if (!isset($_SESSION['user_id'])) {
      // Redirect to login if the user is not authenticated
      header('Location: /login');
      exit;
    }

    $userId = $_SESSION['user_id'];
    $orders = [];

    try {

      $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ?");
      $stmt->bind_param("i", $userId);

      $stmt->execute();
      $result = $stmt->get_result();

      // Fetch all orders
      while ($order = $result->fetch_assoc()) {
        $orders[] = $order;
      }

      $stmt->close();

      // Return the orders
      return $orders;
    } catch (mysqli_sql_exception $e) {
      // Handle database errors
      return ['error' => 'Error retrieving orders: ' . $e->getMessage()];
    }
  }
}
