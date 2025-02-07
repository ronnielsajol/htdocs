<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

// Get product ID from the query parameter
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

if (!$product_id) {
  header('Location: /'); // Redirect to the main page if no ID is provided
  exit();
}

$db = new CartModel();
$productData = $db->getProductById($product_id);

if (!$productData) {
  echo "Product not found.";
  exit();
}

$product = $productData['product'];
$nextProducts = $productData['nextProducts'];

include 'shared/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($product['name']); ?></title>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <link rel="stylesheet" href="../../css/product.css">
</head>

<body>
  <main class="product-details">
    <div class="product-image">
      <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
    </div>
    <div class="product-info">
      <h1><?php echo htmlspecialchars($product['name']); ?></h1>
      <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
      <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p>
      <p class="product-stock"><?php echo $product['quantity'] > 0 ? "In Stock: {$product['quantity']}" : "Out of Stock"; ?></p>

      <div class="item-quantity">
        <label for="quantity-<?php echo $product['id']; ?>">Quantity:</label>
        <button class="decrease" type="button" onclick="adjustQuantity('<?php echo $product['id']; ?>', -1)">-</button>
        <input type="number" id="quantity-<?php echo $product['id']; ?>" name="quantity" min="1" max="<?php echo $product['quantity']; ?>" class="quantity-input" value="1">
        <button class="increase" type="button" onclick="adjustQuantity('<?php echo $product['id']; ?>', 1)">+</button>
      </div>
      <button class="add-to-cart-btn" type="button" <?php echo $product['quantity'] === 0 ? "disabled" : ""; ?> data-product-id="<?php echo $product['id'] ?>">
        <?php echo $product['quantity'] === 0 ? "Out of Stock" : "Add to Cart"; ?>
      </button>
    </div>
  </main>

  <section class="related-products">
    <h2>Related Products</h2>
    <div class="product-grid">
      <?php foreach ($nextProducts as $nextProduct): ?>
        <div class="related-item-card">
          <a href="/product?product_id=<?php echo $nextProduct['id']; ?>">
            <img src="<?php echo htmlspecialchars($nextProduct['image']); ?>"
              alt="<?php echo htmlspecialchars($nextProduct['name']); ?>"
              class="related-item-image">
            <h3 class="related-item-name"><?php echo htmlspecialchars($nextProduct['name']); ?></h3>
            <p class="related-item-price">₱<?php echo number_format($nextProduct['price'], 2); ?></p>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script>
    function adjustQuantity(productId, delta) {
      const quantityInput = document.getElementById(`quantity-${productId}`);
      let currentValue = parseInt(quantityInput.value);
      const maxQuantity = parseInt(quantityInput.max);
      const minQuantity = parseInt(quantityInput.min);

      currentValue += delta;

      if (currentValue >= minQuantity && currentValue <= maxQuantity) {
        quantityInput.value = currentValue;
      }
    }

    document.querySelector('.add-to-cart-btn').addEventListener('click', function() {
      const productId = this.dataset.productId;
      const quantity = document.getElementById(`quantity-${productId}`).value;
      
      // Add to cart logic here
      console.log(`Adding product ${productId} to cart with quantity ${quantity}`);
      

    });
  </script>
</body>
</html>

<?php include 'shared/footer.php'; ?>

