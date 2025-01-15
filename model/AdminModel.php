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

  public function register($username, $password, $email)
  {
    // Check if the username or email already exists
    $query = "SELECT * FROM admin WHERE username = ? OR email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      return "Username or email already exists.";
    }

    // Insert the new admin into the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO admin (username, password, email) VALUES (?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('sss', $username, $hashedPassword, $email);

    if ($stmt->execute()) {
      return true; // Registration successful
    } else {
      return "Error: " . $this->conn->error; // Registration failed
    }
  }

  // Login method
  public function login($username, $password)
  {
    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $admin = $result->fetch_assoc();
      // Verify the password
      if (password_verify($password, $admin['password'])) {
        return $admin; // Return admin data on success
      }
    }
    return false; // Login failed
  }

  // Get all users
  public function getAllUsers()
  {
    $sql = "SELECT * FROM users WHERE role = 'user'";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  // Get all merchants
  public function getAllMerchants()
  {
    $sql = "SELECT * FROM users WHERE role = 'merchant'";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  // Get all orders
  public function getAllOrders()
  {
    $sql = "SELECT * FROM orders";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  // Get total statistics
  public function getStatistics()
  {
    $stats = [];

    // Total users
    $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
    $stats['total_users'] = $result->fetch_assoc()['count'];

    // Total merchants
    $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'merchant'");
    $stats['total_merchants'] = $result->fetch_assoc()['count'];

    // Total orders
    $result = $this->conn->query("SELECT COUNT(*) as count FROM orders");
    $stats['total_orders'] = $result->fetch_assoc()['count'];

    // Total revenue
    $result = $this->conn->query("SELECT SUM(total_amount) as revenue FROM orders");
    $stats['total_revenue'] = $result->fetch_assoc()['revenue'];

    return $stats;
  }
}
