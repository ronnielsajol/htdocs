<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/UserModel.php';

class UserController
{

  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }
  public function handleRegister()
  {
    header('Content-Type: application/json'); // Set JSON header
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
      exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['username'], $data['email'], $data['password'], $data['confirm_password'])) {
      echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
      exit;
    }

    $username = trim($data['username']);
    $email = trim($data['email']);
    $password = trim($data['password']);
    $confirmPassword = trim($data['confirm_password']);

    if ($password !== $confirmPassword) {
      echo json_encode(['success' => false, 'message' => 'Passwords do not match!']);
      exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $hashedPassword);

    try {
      if ($stmt->execute()) {
        echo json_encode(['success' => true, 'redirect' => '/', 'message' => 'Registration successful! You can now log in.']);
        exit;
      }
    } catch (mysqli_sql_exception $e) {
      if ($e->getCode() === 1062) {
        if (strpos($e->getMessage(), 'username') !== false) {
          echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
        } elseif (strpos($e->getMessage(), 'email') !== false) {
          echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
        } else {
          echo json_encode(['success' => false, 'message' => 'Duplicate entry detected.']);
        }
      } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
      }
    } finally {
      $stmt->close();
    }
  }



  public function handleLogin()
  {
    header('Content-Type: application/json'); // Set JSON header
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
      exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['username']) || !isset($data['password'])) {
      echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
      exit;
    }

    $username = trim($data['username']);
    $password = trim($data['password']);

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();

      if ($user['role'] === 'merchant') {
        echo json_encode(['success' => false, 'message' => 'Login not allowed for merchants.']);
        exit;
      }

      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];

        echo json_encode(['success' => true, 'redirect' => '/home']);
        exit;
      } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password.']);
        exit;
      }
    } else {
      echo json_encode(['success' => false, 'message' => 'User not found.']);
      exit;
    }
  }


  public function showOrders()
  {
    $db = new UserModel();
    $orders =  $db->getOrders();

    if (isset($orders['error'])) {
      echo $orders['error']; // Display the error if any
    }
  }

  public function showUserOrders()
  {
    if (!isset($_SESSION['user_id'])) {
      // Redirect to login if the user is not authenticated
      header('Location: /login');
      exit;
    }

    $user_id = $_SESSION['user_id'];
    require_once __DIR__ . '/../model/orderModel.php';

    $orderModel = new OrderModel();
    $orders = $orderModel->getUserOrders($user_id);
    echo "<pre>";
    echo "User ID: " . htmlspecialchars($user_id) . "\n";
    print_r($orders);
    echo "</pre>";

    if (isset($orders['error'])) {
      echo $orders['error'];
      exit;
    }

    // Pass orders to the view
    require __DIR__ . '/../views/orders-page.php';
  }
}
