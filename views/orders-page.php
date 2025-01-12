<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';

$user_id = $_SESSION['user_id'] ?? null;

$oderModel = new OrderModel();
$orders = $oderModel->getUserOrders($user_id);



include 'shared/header.php';
?>

<!DOCTYPE html>
<html>

<head>
  <title>Your Orders</title>
</head>

<body>
  <h1>Your Orders</h1>
  <main>
    <?php if (empty($orders)): ?>
      <p>You have no orders yet.</p>
    <?php else: ?>
      <table border="1">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Transaction Number</th>
            <th>Total Amount</th>
            <th>Order Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><?= htmlspecialchars($order['id']) ?></td>
              <td><?= htmlspecialchars($order['transaction_number']) ?></td>
              <td><?= htmlspecialchars(number_format($order['total_amount'], 2)) ?></td>
              <td><?= htmlspecialchars($order['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>

  <a href="/home">Back to Home</a>
</body>

<?php include 'shared/footer.php'; ?>


</html>