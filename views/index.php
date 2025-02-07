<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

$db = new CartModel();

$search = $_GET['search'] ?? '';
$sortBy = $_GET['sort_by'] ?? null;
$order = $_GET['order'] ?? null;

$products = $db->getAllProducts($search, $sortBy, $order);
$totalProducts = count($products);

// Number of products to display initially and load each time
$displayCount = 40;

// Initialize the session
SessionHelper::init();
// Get the current user's ID
$user_id = SessionHelper::get('user_id');
include 'shared/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Search</title>
    <!-- Include your existing CSS and other head elements here -->
    <style>
            /* Add this to your existing styles */
     /* Add this CSS to your stylesheet */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        /* Item card styles */
        .item-card {
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            transition: box-shadow 0.3s ease;
        }

        .item-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .item-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-name {
            margin: 10px 0;
            font-size: 1.1em;
        }

        .item-price {
            font-weight: bold;
            margin: 5px 0;
        }

        .add-to-cart-btn {
            margin-top: auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Search and filter forms */
        .search-forms {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            gap: 10px;
        }

        .search-form, .filter-form {
            gap: 10px;
            flex: 1;
            margin: 10px;
        }


        /* Responsive styles */
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .row {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .filter-form {
                width: 100%;
            }
        }

        @media (max-width: 600px) {
            .search-forms {
                flex-direction: column;
            }

            .search-form, .filter-form {
                width: 100%;
                margin: 0 0 10px 0;
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr); /* Adjusted for two columns */
            }

            .item-card {
                max-width: 100%;
            }

            /* Loading spinner styles */
            .loading {
                margin: auto;
                align-items: center;
                justify-content: center;
                gap: 10px;
                font-size: 16px;
                color: #333;
                font-family: Arial, sans-serif;
            }

            /* Styling for the spinner animation */
            .spinner {
                margin: auto;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #3498db; /* Change color as needed */
                border-radius: 50%;
                width: 30px;
                height: 30px;
                animation: spin 1s linear infinite;
            }

            /* Spinner animation */
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Style for the text */
            .loading span {
                font-size: 16px;
                color: #333; /* Adjust color as needed */
                font-weight: bold;
            }
             .item-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .item-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .item-card-link {
                display: block;
                text-decoration: none;
                color: inherit;
                height: 100%;
            }

            .item-card-link:hover {
                text-decoration: none;
            }

            .item-name {
                margin: 10px 0;
                font-size: 1.1em;
                color: #333;
            }

            .item-price {
                font-weight: bold;
                color: #e74c3c;
            }

            .out-of-stock {
                color: #e74c3c;
                font-weight: bold;
            }
        }

        .pagination-summary {
            /* Remove or comment out the pagination styles */
            /*margin-bottom: 10px;*/
        }
    </style>
</head>
<body>
    <main class="product-container">
        <div class="search-forms">
            <!-- Your existing search and filter forms go here -->
        </div>
        <div id="product-container" class="product-grid">
            <!-- Products will be loaded here by JavaScript -->
        </div>
        <div id="loading" class="loading" style="display: none;">
            <div class="spinner"></div>
            <span>Loading more products...</span>
        </div>
    </main>

    <script>
        const allProducts = <?php echo json_encode($products); ?>;
        const displayCount = <?php echo $displayCount; ?>;
        let currentIndex = 0;

        function loadMoreProducts() {
            const container = document.getElementById('product-container');
            for (let i = 0; i < displayCount; i++) {
                if (currentIndex >= allProducts.length) {
                    currentIndex = 0; // Reset to start if we've reached the end
                }
                const product = allProducts[currentIndex];
                const productElement = createProductElement(product);
                container.appendChild(productElement);
                currentIndex++;
            }
        }

        function createProductElement(product) {
            const div = document.createElement('div');
            div.className = `item-card ${product.quantity === 0 ? 'out-of-stock-card' : ''}`;
            div.innerHTML = `
                <a href="/product?product_id=${product.id}" class="item-card-link">
                    <img src="${product.image}" alt="${product.name}" class="item-image" width="260" height="auto">
                    <h2 class="item-name">${product.name}</h2>
                    <p class="out-of-stock">${product.quantity === 0 ? "OUT OF STOCK" : ""}</p>
                    <p class="item-price">₱${parseFloat(product.price).toFixed(2)}</p>
                </a>
            `;
            return div;
        }


        function isNearBottom() {
            return window.innerHeight + window.scrollY >= document.body.offsetHeight - 500;
        }

        window.addEventListener('scroll', () => {
            if (isNearBottom()) {
                document.getElementById('loading').style.display = 'block';
                setTimeout(() => {
                    loadMoreProducts();
                    document.getElementById('loading').style.display = 'none';
                }, 500); // Simulate loading delay
            }
        });

        // Initial load
        loadMoreProducts();
    </script>

    <?php include 'shared/footer.php'; ?>
</body>
</html>

