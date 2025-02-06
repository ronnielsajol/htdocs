<?php

if (!isset($_SESSION['merchant_id'])) {
    header("Location: auth/merchant-login.php");
    exit();
}

$merchant_username = $_SESSION['merchant_username'];
$merchant_id = $_SESSION['merchant_id'];


$db = new CartModel();
$merchantProducts = $db->getProductsForMerchant($merchant_id);


include 'shared/merchant-header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

    <main>
        <section class="dashboard-content">

            <div class="dashboard-header">
                <h2>Your Products</h2>
                <a href="/merchant/products/add" class="add-product">Add Product</a>
            </div>

            <?php if (!empty($merchantProducts)) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($merchantProducts as $product) : ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['description']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                                <td>
                                    <a href="/merchant/products/edit?id=<?php echo $product['id']; ?>"><i class="fa-solid fa-pen-to-square"></i></a> |
                                    <form action="/merchant/products/delete" method="post" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this product?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No products found. <a href="add-product.php">Add a new product</a></p>
            <?php endif; ?>
        </section>
    </main>


</body>
<?php include 'shared/footer.php'; ?>

</html>