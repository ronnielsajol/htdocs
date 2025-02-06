<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/UserModel.php';
require_once __DIR__ . '/../utils/JWTHandler.php'; // Include JWTHandler

class UserController
{
  public function handleRegister()
  {
    header('Content-Type: application/json'); // Set JSON header

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
        echo json_encode(['success' => true, 'message' => 'Registration successful! You can now log in.']);
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

    $stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE username = ?");
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
        // ✅ Generate JWT Token
        $jwtHandler = new JWTHandler();
        $token = $jwtHandler->generateToken($user);

        echo json_encode(['success' => true, 'token' => $token, 'username' => $username, 'redirect' => '/home']);
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
    header('Content-Type: application/json');

    // ✅ Get token from Authorization Header
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
      echo json_encode(['success' => false, 'message' => 'Unauthorized: Missing token']);
      exit;
    }

    $token = $matches[1];

    try {
      // ✅ Validate Token
      $jwtHandler = new JWTHandler();
      $decoded = $jwtHandler->validateToken($token);
      $user_id = $decoded->user_id;
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Unauthorized: ' . $e->getMessage()]);
      exit;
    }

    require_once __DIR__ . '/../model/orderModel.php';

    $orderModel = new OrderModel();
    $orders = $orderModel->getUserOrders($user_id);

    if (isset($orders['error'])) {
      echo json_encode(['success' => false, 'message' => $orders['error']]);
      exit;
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
  }
}
