<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/OrderModel.php';

$user_id = $_SESSION['user_id'] ?? null;

$orderModel = new OrderModel();
$orders = $orderModel->getUserOrders($user_id);

include 'shared/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Orders</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333;
      margin: 0;
      padding: 10px;
      background-color: #f4f4f4;
      font-size: 16px;
    }

    h1 {
      color: #2c3e50;
      text-align: center;
      margin-bottom: 20px;
      margin-top: 10%;
      font-size: 24px;
    }

    main {
      background-color: #fff;
      padding: 15px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .order {
      margin-bottom: 20px;
      padding: 15px;
      background-color: #f9f9f9;
      border-radius: 5px;
      transition: box-shadow 0.3s ease;
    }

    .order:hover {
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      color: #3498db;
      margin-bottom: 10px;
      font-size: 20px;
    }

    h3 {
      color: #2c3e50;
      margin-top: 15px;
      font-size: 18px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
      font-weight: bold;
    }

    img {
      max-width: 80px;
      height: auto;
      border-radius: 5px;
    }

    .back-link {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 15px;
      background-color: #3498db;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s ease;
      font-size: 16px;
      width: 80%;
      margin: auto;
    }

    .back-link:hover {
      background-color: #2980b9;
    }

    .order-toggle {
      cursor: pointer;
      user-select: none;
    }

    .order-details {
      display: none;
    }

    .order-details.show {
      display: block;
    }

    .toggle-icon {
      float: right;
    }

    @media (min-width: 481px) {
      body {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
      }

      h1 {
        font-size: 28px;
      }

      main {
        padding: 20px;
      }

      .order {
        padding: 20px;
      }

      h2 {
        font-size: 22px;
      }

      h3 {
        font-size: 20px;
      }

      th, td {
        padding: 12px;
      }

      img {
        max-width: 100px;
      }

      .back-link {
        padding: 10px 20px;
      }
    }
  </style>
</head>

<body>
  <h1>Your Orders</h1>
  <main>
    <?php if (empty($orders)): ?>
      <p>You have no orders yet.</p>
    <?php else: ?>

      <?php foreach ($orders as $order): ?>
        <section class="order">
          <h2 class="order-toggle" data-order-id="<?= htmlspecialchars($order['id']) ?>">
            Order ID: <?= htmlspecialchars($order['id']) ?>
            <span class="toggle-icon">▼</span>
          </h2>
          <div class="order-details" id="order-<?= htmlspecialchars($order['id']) ?>">
            <p><strong>Transaction Number:</strong> <?= htmlspecialchars($order['transaction_number']) ?></p>
            <p><strong>Total Amount:</strong> ₱<?= htmlspecialchars(number_format($order['total_amount'], 2)) ?></p>
            <p><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>

            <h3>Products in this Order:</h3>
            <?php if (empty($order['products'])): ?>
              <p>No products found for this order.</p>
            <?php else: ?>
              <table>
                <thead>
                  <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($order['products'] as $product): ?>
                    <tr>
                      <td>
                        <img src="<?= htmlspecialchars($product['product_image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                      </td>
                      <td><?= htmlspecialchars($product['product_name']) ?></td>
                      <td><?= htmlspecialchars($product['quantity']) ?></td>
                      <td>$<?= htmlspecialchars(number_format($product['price'], 2)) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </section>
      <?php endforeach; ?>

    <?php endif; ?>
  </main>

  <a href="/home" class="back-link">Back to Home</a>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const orderToggles = document.querySelectorAll('.order-toggle');
      orderToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
          const orderId = this.getAttribute('data-order-id');
          const orderDetails = document.getElementById(`order-${orderId}`);
          orderDetails.classList.toggle('show');
          const toggleIcon = this.querySelector('.toggle-icon');
          toggleIcon.textContent = orderDetails.classList.contains('show') ? '▲' : '▼';
        });
      });
    });
  </script>
</body>

<?php include 'shared/footer.php'; ?>

</html>

