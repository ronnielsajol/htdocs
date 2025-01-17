<?php

require_once __DIR__ . '/router.php';
require_once __DIR__ . '/controller/UserController.php';
require_once __DIR__ . '/controller/MerchantController.php';
require_once __DIR__ . '/controller/AdminController.php';
require_once __DIR__ . '/controller/CartController.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';


// ##################################################
// ##################################################
// ##################################################

// Static GET
// In the URL -> http://localhost
// The output -> Index

// Guest routes
get('/', function () {
    AuthMiddleware::handleGuestOnly();
    require 'views/auth/login.php';
});

get('/register', function () {
    AuthMiddleware::handleGuestOnly();
    require 'views/auth/register.php';
});



get('/admin/dashboard', function () {
    AuthMiddleware::handleAdminAuth();
    require 'views/admin/admin-dashboard.php';
});

get('/admin/login', function () {
    require 'views/admin/admin-login.php';
});

// Admin Login (POST) - Handle login form submission
post('/admin/login', function () {
    AuthMiddleware::handleGuestOnly(); // Ensure guest access only
    $controller = new adminController();
    $message = $controller->handleLogin();
    require_once __DIR__ . '/views/auth/admin-login.php';
});

// Admin Register (GET) - Display the registration page
get('/admin/register', function () {
    require 'views/admin/admin-register.php';
});

// Admin Register (POST) - Handle registration form submission
post('/admin/register', function () {
    AuthMiddleware::handleGuestOnly(); // Ensure guest access only
    $controller = new AdminController();
    $message = $controller->handleRegister();
});

get('/admin/logout', function () {
    AuthMiddleware::handleAdminAuth();
    require 'views/auth/logout.php';
});


get('/merchant/login', function () {
    AuthMiddleware::handleGuestOnly();
    require 'views/auth/merchant-login.php';
});

get('/merchant/register', function () {
    AuthMiddleware::handleGuestOnly();
    require 'views/auth/merchant-register.php';
});

// Protected user routes
get('/home', function () {
    AuthMiddleware::handleUserAuth();
    require 'views/index.php';
});

get('/orders', function () {
    AuthMiddleware::handleUserAuth(); // Ensure the user is authenticated
    $controller = new UserController(); // Assuming orders are part of UserController
    $orders = $controller->showOrders(); // 
    require 'views/orders-page.php'; // The view to display the user's orders
});

get('/logout', function () {
    AuthMiddleware::handleUserAuth();
    require 'views/auth/logout.php';
});

// Protected merchant routes
get('/merchant/dashboard', function () {
    AuthMiddleware::handleMerchantAuth();
    require 'views/merchant-dashboard.php';
});

get('/merchant/logout', function () {
    AuthMiddleware::handleMerchantAuth();
    require 'views/auth/logout.php';
});

get('/merchant/products/edit', function () {
    AuthMiddleware::handleMerchantAuth();


    if (!isset($_GET['id'])) {
        die('Product ID is required to edit the product.');
    }
    require 'views/merchant/edit-product.php';
});

get('/order/confirmation', function () {
    include __DIR__ . '/views/order_confirmation.php';
});

get('/orders', function () {
    $userController = new UserController();
    $userController->showUserOrders();
});

// MerchantController Routes


get('/merchant/products/add', function () {
    AuthMiddleware::handleMerchantAuth();
    require 'views/merchant/add-product.php';
});

post('/merchant/products/create', function () {
    AuthMiddleware::handleMerchantAuth();
    $controller = new MerchantController();
    $message = $controller->handleProductAdd();
    echo $message; // Display any errors or success messages
});

post('/merchant/products/update', function () {
    AuthMiddleware::handleMerchantAuth();
    $controller = new MerchantController();
    $message = $controller->handleProductUpdate();
    echo $message; // Display any errors or success messages
});

post('/merchant/products/delete', function () {
    AuthMiddleware::handleMerchantAuth();
    $controller = new MerchantController();
    $message = $controller->handleProductDelete();
    echo $message;
});



// Auth routes
post('/login', function () {
    AuthMiddleware::handleGuestOnly();
    $controller = new UserController();
    $message = $controller->handleLogin();
    require_once __DIR__ . '/views/auth/login.php';
});

post('/register', function () {
    AuthMiddleware::handleGuestOnly();
    $controller = new UserController();
    $message = $controller->handleRegister();
    require_once __DIR__ . '/views/auth/register.php'; // You can create a register view for form submission
});

post('/merchant/login', function () {
    AuthMiddleware::handleGuestOnly();
    $controller = new MerchantController();
    $message = $controller->handleLogin();
    require_once __DIR__ . '/views/auth/merchant-login.php';
});

post('/merchant/register', function () {
    AuthMiddleware::handleGuestOnly();
    $controller = new MerchantController();
    $message = $controller->handleRegister();
    require_once __DIR__ . '/views/auth/merchant-register.php';
});

// CartController Routes
get('/cart', function () {
    AuthMiddleware::handleUserAuth();
    require 'views/cart.php';
});


get('/cart/count', function () {
    AuthMiddleware::handleUserAuth();
    $controller = new CartController();
    echo json_encode($controller->getCartItemCount());
});

post('/cart/add', function () {
    $controller = new CartController();
    echo json_encode($controller->addToCart());
});

post('/cart/update', function () {
    $controller = new CartController();
    echo json_encode($controller->updateCart());
});

post('/cart/remove', function () {
    $controller = new CartController();
    $response = $controller->removeFromCart(); // Get the return value
    echo json_encode($response);
});


get('/cart/summary', function () {
    AuthMiddleware::handleUserAuth();
    require 'views/checkout-page.php';
});

post('/cart/checkout', function () {
    AuthMiddleware::handleUserAuth();
    $controller = new CartController();
    $message = $controller->checkout();
    echo json_encode($message);
});

get('/product', function () {
    $controller = new CartController();
    $message = $controller->getProductById();

    require 'views/product-page.php';
});





get('/seed-database', function () {
    // Optional: Add some kind of protection to ensure this is not exposed in production
    if ($_SERVER['REMOTE_ADDR']  !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') { // Allow only local access
        header('HTTP/1.0 403 Forbidden');
        echo 'Access denied.';
        exit();
    }

    require_once __DIR__ . '/includes/database-seeder.php'; // Include the seeder file
    echo 'Database seeding completed.';
});


get('/migrate', function () {
    // Optional: Add some kind of protection to ensure this is not exposed in production
    if ($_SERVER['REMOTE_ADDR']  !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') { // Allow only local access
        header('HTTP/1.0 403 Forbidden');
        echo 'Access denied.';
        exit();
    }

    require_once __DIR__ . '/includes/migrate.php'; // Include the seeder file
    echo 'migrate completed.';
});

get('/seed-products', function () {
    $merchantId = $_GET['merchant_id'] ?? null;
    $productCount = $_GET['product_count'] ?? null;

    if (!$merchantId || !$productCount) {
        echo "Please provide both 'merchant_id' and 'product_count' as query parameters.";
        return;
    }
    require_once __DIR__ . '/model/Config.php';
    require_once __DIR__ . '/includes/merchant-seeder.php';

    seedProductsForMerchant($conn, $merchantId, $productCount);
});




// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404', 'views/404.php');
