<?php

require_once __DIR__ . '/Database.php';

class AdminModel
{
  private $conn;

  public function __construct()
  {
    $database = new Database();
    $this->conn = $database->getConnection();
  }

  public function getAllUsers()
  {
    $sql = "SELECT * FROM users WHERE role = 'user'";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllMerchants()
  {
    $sql = "SELECT * FROM users WHERE role = 'merchant'";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getAllOrders()
  {
    $sql = "SELECT * FROM orders";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  public function getStatistics()
  {
    $stats = [];

    $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
    $stats['total_users'] = $result->fetch_assoc()['count'];

    $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'merchant'");
    $stats['total_merchants'] = $result->fetch_assoc()['count'];

    $result = $this->conn->query("SELECT COUNT(*) as count FROM orders");
    $stats['total_orders'] = $result->fetch_assoc()['count'];

    $result = $this->conn->query("SELECT SUM(total_amount) as revenue FROM orders");
    $stats['total_revenue'] = $result->fetch_assoc()['revenue'];

    return $stats;
  }
}
