<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

// Initialize the session
SessionHelper::init();

// Get the current user's ID
$user_id = SessionHelper::get('user_id');


// Now you can use $user_id in your cart.php
$cartModel = new CartModel();
$cart_items = $cartModel->getCartItems($user_id);

$cart_total = 0;
if (!empty($cart_items)) {
    $cart_total = array_reduce($cart_items, function ($total, $item) {
        return $total + ($item['price'] * $item['quantity']);
    }, 0);
}

include 'shared/header.php';
?>

<head>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

</head>


<body>
    <main class="cart-page">
        <h2>Your Shopping Cart</h2>
        <div class="cart-items">
            <?php if (empty($cart_items)): ?>
                <p>Your cart is empty.</p>
            <?php else: ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item <?php echo $item['quantity'] > $item['stock'] ? 'out-of-stock-warning' : ''; ?>"
                        data-product-id="<?php echo $item['product_id']; ?>">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                            class="item-image">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="item-price">₱<?php echo number_format($item['price'], 2); ?></p>
                            <div class="item-quantity">
                                <label for="quantity-<?php echo $item['product_id']; ?>">Quantity</label>
                                <button class="decrease">-</button>
                                <input type="number"
                                    id="quantity-<?php echo $item['product_id']; ?>"
                                    name="quantity"
                                    value="<?php echo $item['quantity']; ?>"
                                    min="1"
                                    max="<?php echo $item['stock']; ?>"
                                    class="quantity-input">
                                <button class="increase">+</button>
                                <span><?php echo $item['stock'] . ' pieces available' ?></span>
                            </div>
                            <?php if ($item['quantity'] > $item['stock']): ?>
                                <p class="error">Quantity exceeds available stock (<?php echo $item['stock']; ?> available).</p>
                            <?php endif; ?>
                            <button class="remove-item" data-product-id="<?php echo $item['product_id']; ?>">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="cart-summary">
            <p>Total: <span class="cart-total">₱<?php echo number_format($cart_total, 2); ?></span></p>
            <form action="/cart/summary" method="get">
                <button class="checkout-btn" <?php echo empty($cart_items) ? 'disabled' : ''; ?>>Proceed to Checkout</button>

            </form>
        </div>
    </main>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</body>

<?php include 'shared/footer.php'; ?>