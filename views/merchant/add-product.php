<?php

require_once './model/Config.php';

$merchantId = $_SESSION['merchant_id'];



include './views/shared/merchant-header.php';

?>


<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
  <div class="container">
    <h1>Add Product</h1>
    <?php if (!empty($message)) : ?>
      <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="/merchant/products/create" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" required>
      </div>

      <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required></textarea>
      </div>

      <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required>
      </div>

      <div class="form-group">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>
      </div>

      <div class="form-group">
        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
      </div>

      <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
  </div>
</body>