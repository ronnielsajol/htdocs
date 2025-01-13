<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
  echo "Invalid order.";
  exit;
}

// Fetch order details from the database
$orderModel = new OrderModel();
$orderDetails = $orderModel->getOrderDetails($order_id); // Create this method if not already implemented

if (!$orderDetails) {
  echo "Order not found.";
  exit;
}


include 'shared/header.php';

?>
<!DOCTYPE html>
<html>

<head>
  <title>Order Confirmation</title>
</head>

<body>
  <main style="display: flex; flex-direction: column; align-items: center;">
    <h1>Thank you for your order!</h1>
    <p>Order Number: <?= htmlspecialchars($orderDetails['transaction_number']) ?></p>
    <p>Total Amount: <?= htmlspecialchars($orderDetails['total_amount']) ?></p>
    <p>We will process your order shortly.</p>
    <a href="/">Return to Home</a>
    <a href="/orders">View Orders</a>
  </main>
</body>


<?php include 'shared/footer.php'; ?>

</html>