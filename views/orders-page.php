<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';

$user_id = $_SESSION['user_id'] ?? null;

$orderModel = new OrderModel();
$orders = $orderModel->getUserOrders($user_id);

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

      <?php foreach ($orders as $order): ?>
        <section class="order">
          <h2>Order ID: <?= htmlspecialchars($order['id']) ?></h2>
          <p>Transaction Number: <?= htmlspecialchars($order['transaction_number']) ?></p>
          <p>Total Amount: <?= htmlspecialchars(number_format($order['total_amount'], 2)) ?></p>
          <p>Order Date: <?= htmlspecialchars($order['created_at']) ?></p>

          <h3>Products in this Order:</h3>
          <?php if (empty($order['products'])): ?>
            <p>No products found for this order.</p>
          <?php else: ?>
            <table border="1">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Product Name</th>
                  <th>Quantity</th>
                  <th>Price</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($order['products'] as $product): ?>
                  <tr>
                    <td>
                      <img src="<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" style="width: 100px; height: auto;">
                    </td>
                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                    <td><?= htmlspecialchars($product['quantity']) ?></td>
                    <td><?= htmlspecialchars(number_format($product['price'], 2)) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </section>
        <hr>
      <?php endforeach; ?>

    <?php endif; ?>
  </main>

  <a href="/home">Back to Home</a>
</body>

<?php include 'shared/footer.php'; ?>

</html>