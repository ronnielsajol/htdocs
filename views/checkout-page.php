<?php

require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

SessionHelper::init();
$userId = $_SESSION['user_id'] ?? 1; // Use session user ID or a fallback dummy ID

$db = new CartModel();
$cartSummary = $db->getCartSummary($userId);

include 'shared/header.php';
?>

<html>

<head>
  <title>Cart Summary</title>
</head>

<body>
  <h1>Cart Summary</h1>
  <!-- <pre><?php print_r($cartSummary) ?></pre> -->

  <main>
    <?php if (!empty($cartSummary['items'])): ?>
      <table border="1">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cartSummary['items'] as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= htmlspecialchars($item['quantity']) ?></td>
              <td><?= htmlspecialchars(number_format($item['price'], 2)) ?></td>
              <td><?= htmlspecialchars(number_format($item['subtotal'], 2)) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" align="right"><strong>Total Amount:</strong></td>
            <td><?= htmlspecialchars(number_format($cartSummary['total'], 2)) ?></td>
          </tr>
        </tfoot>
      </table>
      <form action="/cart/checkout" method="POST">
        <button type="submit">Place Order</button>
      </form>
    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </main>
</body>

<?php include 'shared/footer.php'; ?>

</html>