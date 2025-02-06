<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../utils/FileUploader.php';


class MerchantController
{

  public function __construct()
  {
    $this->startSession();
  }

  private function startSession()
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
      $role = 'merchant';

      //validation
      if ($password !== $confirmPassword) {
        $message = 'Passwords do not match!';
      } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");

        $stmt->bind_param('ssss', $username, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
          session_start();
          $_SESSION['register_success'] = "Registration successful! You can now log in.";

          header('Location: /merchant/login');
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

      $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = 'merchant'");
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
          $_SESSION['merchant_id'] = $user['id'];
          $_SESSION['merchant_username'] = $user['username'];
          $_SESSION['role'] = $user['role'];

          header('Location: /merchant/dashboard');
        } else {
          $message = 'Invalid username or password';
        }
      } else {
        $message = 'Invalid username or password';
      }
    }

    $_SESSION['login_message'] = $message;
  }

  public function handleProductUpdate()
  {
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Validate the request
      if (!isset($_POST['product_id'], $_POST['name'], $_POST['price'], $_POST['quantity'], $_POST['description'])) {
        $message = 'All fields are required.';
        return $message;
      }

      $productId = intval($_POST['product_id']);
      $name = trim($_POST['name']);
      $price = floatval($_POST['price']);
      $quantity = intval($_POST['quantity']);
      $description = trim($_POST['description']);
      $newImagePath = null;

      // Check if a new image is uploaded
      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newImageName = date('dmY') . "-product-" . $productId . '.' . $imageExtension;
        $newImagePath = '/uploads/images/' . $newImageName; // Corrected path with separator

        // Move the new image to the uploads directory
        $uploadDirectory = __DIR__ . '/../uploads/images/';
        if (!is_dir($uploadDirectory)) {
          mkdir($uploadDirectory, 0755, true); // Ensure the directory exists
        }

        if (!move_uploaded_file($imageTmpPath, $uploadDirectory . $newImageName)) {
          $message = 'Error uploading the new image.';
          return $message;
        }
      }

      // Database connection
      $db = new Database();
      $conn = $db->getConnection();

      try {
        // Begin a transaction
        $conn->begin_transaction();

        // Retrieve the old image path
        $oldImagePath = null;
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $stmt->bind_result($oldImagePath);
        $stmt->fetch();
        $stmt->close();

        // Update the product in the database
        if ($newImagePath) {
          // If a new image is uploaded, update the image path in the database
          $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, quantity = ?, description = ?, image = ? WHERE id = ?");
          $stmt->bind_param('sdissi', $name, $price, $quantity, $description, $newImagePath, $productId);
        } else {
          // If no new image is uploaded, keep the current image path
          $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, quantity = ?, description = ? WHERE id = ?");
          $stmt->bind_param('sdisi', $name, $price, $quantity, $description, $productId);
        }

        if ($stmt->execute()) {
          // Commit the transaction
          $conn->commit();

          // Delete the old image file if a new image is uploaded
          if ($newImagePath && $oldImagePath && file_exists(__DIR__ . '/..' . $oldImagePath)) {
            unlink(__DIR__ . '/..' . $oldImagePath);
          }

          $message = 'Product updated successfully.';
          header('Location: /merchant/dashboard'); // Redirect to the dashboard or products page
          exit;
        } else {
          $conn->rollback();
          $message = 'Error updating product: ' . $stmt->error;
        }
        $stmt->close();
      } catch (Exception $e) {
        $conn->rollback();
        $message = 'Error: ' . $e->getMessage();
      }
    }
    return $message;
  }



  public function handleProductDelete()
  {
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Validate the request
      if (!isset($_POST['product_id'])) {
        $message = 'Product ID is required.';
        return $message;
      }

      $productId = intval($_POST['product_id']);

      // Database connection
      $db = new Database();
      $conn = $db->getConnection();

      try {
        // Begin a transaction
        $conn->begin_transaction();

        // Retrieve the image path of the product
        $imagePath = null; // Initialize the variable to prevent unassigned usage
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $stmt->bind_result($imagePath);
        $stmt->fetch();
        $stmt->close();

        // Delete the product from the cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE product_id = ?");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $stmt->close();

        // Delete the product from the products table
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param('i', $productId);

        if ($stmt->execute()) {
          // Commit the transaction
          $conn->commit();

          // Delete the image file if it exists
          if (!empty($imagePath) && file_exists(__DIR__ . '/..' . $imagePath)) {
            unlink(__DIR__ . '/..' . $imagePath);
          }

          $message = 'Product deleted successfully.';
          header('Location: /merchant/dashboard'); // Redirect to the dashboard or products page
          exit;
        } else {
          $conn->rollback();
          $message = 'Error deleting product: ' . $stmt->error;
        }
        $stmt->close();
      } catch (Exception $e) {
        $conn->rollback();
        $message = 'Error: ' . $e->getMessage();
      }
    }
    return $message;
  }



  public function handleProductAdd()
  {
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
        // Validate the request
        if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['price'])) {
          throw new Exception('Please fill in all fields.');
        }

        $name = trim($_POST['name']);
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);
        $description = trim($_POST['description']);
        $merchantId = $_SESSION['merchant_id'];

        // Check for file upload errors first
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
          throw new Exception('Error uploading the image: ' . $_FILES['image']['error']);
        }

        // Check for valid image format
        $validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $validImageTypes)) {
          throw new Exception('Invalid image format. Only JPEG, PNG, and GIF are allowed.');
        }

        // Database connection
        $db = new Database();
        $conn = $db->getConnection();

        $uploader = new FileUploader(__DIR__ . '/../uploads/images');
        $imageFileName = $uploader->upload($_FILES['image'], $merchantId, null); // No product ID yet

        // Insert product data into the database
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity, merchant_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $imagePath = '/uploads/images/' . $imageFileName;
        $stmt->bind_param('ssdiss', $name, $description, $price, $quantity, $merchantId, $imagePath);

        if (!$stmt->execute()) {
          throw new Exception('Error adding product: ' . $stmt->error);
        }

        // Product added successfully
        $message = 'Product added successfully.';
        header('Location: /merchant/dashboard');
        exit;
      } catch (Exception $e) {
        $message = $e->getMessage();
      }
    }
    return $message;
  }
}
