<?php

require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../model/UserModel.php';

SessionHelper::init();
$userId = $_SESSION['user_id'] ?? null;

$db = new UserModel();
$orders = $db->getOrders($userId);

?>

<html>

<head></head>

<body>
  <h1>test</h1>
  <?php
  // Example to display the orders
  if (isset($orders) && count($orders) > 0):
  ?>
    <h2>Your Orders</h2>
    <table>
      <tr>
        <th>Order ID</th>
        <th>Date</th>
        <th>Status</th>
        <th>Total</th>
      </tr>
      <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= $order['order_id'] ?></td>
          <td><?= $order['created_at'] ?></td>
          <td><?= $order['status'] ?></td>
          <td><?= $order['total'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php else: ?>
    <p>No orders found.</p>
  <?php endif; ?>
</body>

</html>