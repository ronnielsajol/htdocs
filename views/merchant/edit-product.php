<?php
require_once './model/Config.php';


if (!isset($_GET['id'])) {
  die('Product ID is required');
}

$product_id = intval($_GET['id']);

$product_id = intval($_GET['id']); // Sanitize input

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die('Product not found');
}

$product = $result->fetch_assoc();

include './views/shared/merchant-header.php';


?>

<html>


<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
  <main>
    <h1>Edit Product</h1>
    <form action="/merchant/products/update" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

      <label for="name">Name:</label>
      <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>

      <label for="price">Price:</label>
      <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" step="0.01" required><br>

      <label for="quantity">Quantity:</label>
      <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required><br>

      <label for="description">Description:</label>
      <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br>

      <label for="image">Product Image:</label>
      <input type="file" id="image" name="image" accept="image/*"><br>

      <?php if (!empty($product['image'])): ?>
        <p>Current Image:</p>
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="Current Product Image" style="max-width: 200px;"><br>
      <?php endif; ?>

      <button type="submit">Save Changes</button>
    </form>
  </main>

</body>

</html>