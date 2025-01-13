<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

$db = new CartModel();

// Get current page from query parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 40; // Define the number of products per page

$productData = $db->getProducts($page, $itemsPerPage);

$products = $productData['products'];
$totalProducts = $productData['totalProducts'];
$totalPages = ceil($totalProducts / $itemsPerPage);


// Initialize the session
SessionHelper::init();
// Get the current user's ID
$user_id = SessionHelper::get('user_id');
include 'shared/header.php';


?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Spice&family=Ubuntu:wght@300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">



</head>

<body>

    <main class="product-container">
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="item-card">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="item-image"
                        width="260px"
                        height="auto">
                    <h2 class="item-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="item-price">₱<?php echo number_format($product['price'], 2); ?></p>
                    <button class="add-to-cart-btn"
                        data-product-id="<?php echo $product['id']; ?>">
                        Add to Cart
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination">
            <a href="?page=<?php echo max(1, $page - 1); ?>"
                <?php echo $page === 1 ? 'disabled' : ''; ?>
                class="pagination-btn <?php echo $page === 1 ? 'disabled' : ''; ?>"
                <?php echo $page === 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                Previous
            </a>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="pagination-btn">Next</a>
            <?php endif; ?>
        </div>
    </main>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</body>
<?php include 'shared/footer.php'; ?>