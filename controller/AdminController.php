<?php

require_once __DIR__ . '/../model/AdminModel.php';

class AdminController
{
  private $adminModel;

  public function __construct()
  {
    $this->adminModel = new AdminModel();
  }

  public function handleRegister()
  {
    $db = new Database();
    $conn = $db->getConnection();


    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $confirmPassword = $_POST['confirm_password'];;

      //validation
      if ($password !== $confirmPassword) {
        $message = 'Passwords do not match!';
      } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");

        $stmt->bind_param('sss', $username, $email, $hashedPassword);

        if ($stmt->execute()) {
          session_start();
          $_SESSION['register_success'] = "Registration successful! You can now log in.";

          header('Location: /admin/login');
          exit;
        } else {
          $message = 'Error: ' . $stmt->error;
        }
        $stmt->close();
      }
    }
    return $message;
  }


  public function handleLogin()
  {
    $message = '';

    // Check if registration was successful
    if (isset($_SESSION['register_success'])) {
      $message = $_SESSION['register_success'];
      unset($_SESSION['register_success']);
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'];
      $password = $_POST['password'];

      $password = trim($password);

      $db = new Database();
      $conn = $db->getConnection();

      $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
          $_SESSION['admin_id'] = $user['id'];
          $_SESSION['admin_username'] = $user['username'];

          header('Location: /admin/dashboard');
        } else {
          $message = 'Invalid username or password';
        }
      } else {
        $message = 'Invalid username or password';
      }
    }

    $_SESSION['login_message'] = $message;
  }

  // Fetch all users
  public function getAllUsers()
  {
    return $this->adminModel->getAllUsers();
  }

  // Fetch all merchants
  public function getAllMerchants()
  {
    return $this->adminModel->getAllMerchants();
  }

  // Fetch all orders
  public function getAllOrders()
  {
    return $this->adminModel->getAllOrders();
  }

  // Fetch statistics
  public function getStatistics()
  {
    return $this->adminModel->getStatistics();
  }
}
