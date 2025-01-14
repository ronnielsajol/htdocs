<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

$db = new CartModel();

// Get current page from query parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 40; // Define the number of products per page
$search = $_GET['search'] ?? ''; // Get search query or default to an empty string
// Get the sorting parameters from the query string
$sortBy = $_GET['sort_by'] ?? null;  // Default to no sorting (natural order)
$order = $_GET['order'] ?? null;  // Default to no specific order

// Fetch the products with sorting options
$productData = $db->getProducts($page, $itemsPerPage, $search, $sortBy, $order);

$products = $productData['products'];
$totalProducts = $productData['totalProducts'];
$totalPages = ceil($totalProducts / $itemsPerPage);;


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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Spice&family=Ubuntu:wght@300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">



</head>

<body>

    <main class="product-container">

        <form method="GET" action="" class="search-form">
            <select name="sort_by">
                <option value="price" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] === 'price' ? 'selected' : ''; ?>>Price</option>
                <option value="name" <?php echo isset($_GET['sort_by']) && $_GET['sort_by'] === 'name' ? 'selected' : ''; ?>>Alphabetically</option>
            </select>
            <!-- Order Dropdown -->
            <select name="order">
                <option value="asc" <?php echo isset($_GET['order']) && $_GET['order'] === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                <option value="desc" <?php echo isset($_GET['order']) && $_GET['order'] === 'desc' ? 'selected' : ''; ?>>Descending</option>
            </select>
        </form>
        <form method="GET" action="" class="search-form">
            <input type="text" name="search" placeholder="Search products..."
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="search-btn"><i class="fa fa-search" aria-hidden="true"></i></button>
        </form>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="item-card <?php echo $product['quantity'] === 0 ? 'out-of-stock-card' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        class="item-image"
                        width="260px"
                        height="auto">
                    <h2 class="item-name"><a href="/product?product_id=<?php echo $product['id']; ?>" class="item-card-link"><?php echo htmlspecialchars($product['name']); ?></a></h2>
                    <p class="out-of-stock"><?php echo $product['quantity'] === 0 ? "OUT OF STOCK" : "" ?></p>
                    <p class="item-price">₱<?php echo number_format($product['price'], 2); ?></p>
                    <button class="add-to-cart-btn"
                        type="button"
                        data-product-id="<?php echo $product['id']; ?>" <?php echo $product['quantity'] === 0 ? "disabled" : "" ?>>
                        <?php echo $product["quantity"] === 0 ? "Sold Out" : "Add to Cart" ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination">
            <a href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo urlencode($search); ?>&sort_by=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($order); ?>"
                class="pagination-btn <?php echo $page === 1 ? 'disabled' : ''; ?>">
                Previous
            </a>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort_by=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($order); ?>"
                    class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <a href="?page=<?php echo min($totalPages, $page + 1); ?>&search=<?php echo urlencode($search); ?>&sort_by=<?php echo urlencode($sortBy); ?>&order=<?php echo urlencode($order); ?>"
                class="pagination-btn <?php echo $page === $totalPages ? 'disabled' : ''; ?>">
                Next
            </a>
        </div>

    </main>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        // Add event listeners to both sort and order dropdowns
        document.querySelectorAll('select').forEach(function(selectElement) {
            selectElement.addEventListener('change', function() {
                // Automatically submit the form when an option is selected
                this.form.submit();
            });
        });
    </script>

</body>
<?php include 'shared/footer.php'; ?>