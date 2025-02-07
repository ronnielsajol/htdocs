<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
  echo "Invalid order.";
  exit;
}

$orderModel = new OrderModel();
$orderDetails = $orderModel->getOrderDetails($order_id);

if (!$orderDetails) {
  echo "Order not found.";
  exit;
}

include 'shared/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333;
      margin: 0;
      padding: 20px;
      background-color: #f4f4f4;
    }

    main {
      max-width: 600px;
      margin: 0 auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h1 {
      color: #2c3e50;
      font-size: 24px;
      margin-bottom: 20px;
    }

    p {
      margin-bottom: 15px;
    }

    .order-details {
      background-color: #f9f9f9;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      padding: 15px;
      margin-bottom: 20px;
    }

    .order-number, .order-total {
      font-weight: bold;
    }

    .button-container {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 20px;
    }

    .button {
      display: inline-block;
      padding: 10px 20px;
      background-color: #3498db;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }

    .button:hover {
      background-color: #2980b9;
    }

    @media (min-width: 481px) {
      h1 {
        font-size: 28px;
      }

      .button-container {
        flex-direction: row;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <main>
    <h1>Thank you for your order!</h1>
    <div class="order-details">
      <p><span class="order-number">Order Number:</span> <?= htmlspecialchars($orderDetails['transaction_number']) ?></p>
      <p><span class="order-total">Total Amount:</span> $<?= htmlspecialchars(number_format($orderDetails['total_amount'], 2)) ?></p>
    </div>
    <p>We will process your order shortly.</p>
    <div class="button-container">
      <a href="/home    " class="button">Return to Home</a>
      <a href="/orders" class="button">View Orders</a>
    </div>
  </main>
</body>
<?php include 'shared/footer.php'; ?>
</html>

