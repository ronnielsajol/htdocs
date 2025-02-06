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
    $db = new Database();
    $conn = $db->getConnection();

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = $_POST['password'];
      $confirmPassword = $_POST['confirm_password'];

      // Basic validation
      if ($password !== $confirmPassword) {
        $message = 'Passwords do not match!';
      } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $hashedPassword);

        try {
          if ($stmt->execute()) {
            // Store success message in session
            session_start();
            $_SESSION['register_success'] = 'Registration successful! You can now log in.';

            // Redirect to the login page
            header('Location: /');
            exit;
          }
        } catch (mysqli_sql_exception $e) {
          // Check for duplicate entry error (error code 1062)
          if ($e->getCode() === 1062) {
            if (strpos($e->getMessage(), 'username') !== false) {
              $message = 'Username is already taken.';
            } elseif (strpos($e->getMessage(), 'email') !== false) {
              $message = 'Email is already registered.';
            } else {
              $message = 'Duplicate entry detected.';
            }
          } else {
            $message = 'Error: ' . $e->getMessage();
          }
        } finally {
          $stmt->close();
        }
      }
    }

    $_SESSION["register_message"] = $message;
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
