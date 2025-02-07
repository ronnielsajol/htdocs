<?php
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

SessionHelper::init();
$userId = $_SESSION['user_id'] ?? 1; // Use session user ID or a fallback dummy ID

$db = new CartModel();
$cartSummary = $db->getCartSummary($userId);

include 'shared/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart Summary</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      color: #333;
      margin: 0;
      padding: 10px;
      background-color: #f4f4f4;
    }

    h1 {
      color: #2c3e50;
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
    }

    main {
      background-color: #fff;
      padding: 15px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .cart-item {
      border-bottom: 1px solid #ddd;
      padding: 10px 0;
    }

    .cart-item:last-child {
      border-bottom: none;
    }

    .item-name {
      font-weight: bold;
    }

    .item-details {
      display: flex;
      justify-content: space-between;
      margin-top: 5px;
    }

    .total {
      font-weight: bold;
      text-align: right;
      margin-top: 20px;
      font-size: 18px;
    }

    .checkout-btn {
      display: block;
      width: 100%;
      padding: 10px;
      background-color: #3498db;
      color: #fff;
      text-align: center;
      text-decoration: none;
      border: none;
      border-radius: 5px;
      margin-top: 20px;
      font-size: 16px;
      cursor: pointer;
    }

    .checkout-btn:hover {
      background-color: #2980b9;
    }

    .empty-cart {
      text-align: center;
      font-style: italic;
      color: #777;
    }

    @media (min-width: 481px) {
      body {
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
      }

      h1 {
        font-size: 28px;
      }

      main {
        padding: 20px;
      }

      .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .item-details {
        display: flex;
        gap: 20px;
      }

      .checkout-btn {
        width: auto;
        padding: 10px 20px;
        float: right;
      }
    }
  </style>
</head>
<body>
  <h1>Cart Summary</h1>
  <main>
    <?php if (!empty($cartSummary['items'])): ?>
      <?php foreach ($cartSummary['items'] as $item): ?>
        <div class="cart-item">
          <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
          <div class="item-details">
            <span>Qty: <?= htmlspecialchars($item['quantity']) ?></span>
            <span>Price: ₱<?= htmlspecialchars(number_format($item['price'], 2)) ?></span>
            <span>Total: ₱<?= htmlspecialchars(number_format($item['subtotal'], 2)) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="total">
        Total Amount: ₱<?= htmlspecialchars(number_format($cartSummary['total'], 2)) ?>
      </div>
      <form action="/cart/checkout" method="POST">
        <button type="submit" class="checkout-btn">Place Order</button>
      </form>
    <?php else: ?>
      <p class="empty-cart">Your cart is empty.</p>
    <?php endif; ?>
  </main>
</body>
<?php include 'shared/footer.php'; ?>
</html>

