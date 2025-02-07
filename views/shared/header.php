<?php

require_once __DIR__ . '/../../model/CartModel.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$root_path = realpath($_SERVER["DOCUMENT_ROOT"]);
$project_root = dirname(dirname(dirname(__FILE__)));
$relative_path = str_replace($root_path, '', $project_root);
$base_url = rtrim($relative_path, '/');


$cartModel = new CartModel();
$itemCount = $cartModel->getCartItemCount($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stack and Shop</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Spice&family=Ubuntu:wght@300;400;700&display=swap" rel="stylesheet">
    <style> 
        /* Header styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background-color: #f8f8f8;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-left {
            display: flex;
            align-items: center;
            margin-left: auto; /* Push it to the right */
        }

        .logo-link {
            text-decoration: none;
            color: #333;
        }

        .header-nav {
            display: flex;
            gap: 1rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cart-icon {
            position: relative;
            text-decoration: none;
            color: #333;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ff4d4d;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
        }

        .user-info {
            position: relative;
        }

        .greet-user {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .popover-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
        }

        .popover-menu a {
            display: block;
            padding: 0.5rem;
            text-decoration: none;
            color: #333;
        }


        /* Responsive styles */
        @media (max-width: 768px) {
            .header {
                flex-wrap: wrap;
            }

            .header-nav {
                display: none;
                width: 100%;
                order: 3;
            }

            .header-nav.active {
                display: flex;
                flex-direction: column;
            }

            .menu-toggle {
                display: block;
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
            }   

            .pagination-summary {
                text-align: center;
                width: 100%;
            }
        }

        @media (min-width: 769px) {

            .menu-toggle {
                position: absolute;
                left: 1rem; /* Keeps it on the left */
                top: 50%;
                transform: translateY(-50%);
            }

            .header-left {
                margin-left: auto; /* Pushes it to the right */
            }
            .header-right {
                margin-right: auto; /* Pushes it to the right */
            }
        }
    </style>
</head>

<body>
   <header class="header">
    <!-- First Row: Logo & Cart -->
    <div class="header-top" style="width: 100%;">
        <div class="header-right">
            <a href="/home" class="logo-link">
                <h1>Stack and Shop</h1>
            </a>
            <div class="header-left">
                <a href="/cart" class="cart-icon" aria-label="View shopping cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="sr-only">Cart</span>
                    <span class="cart-count"><?php echo htmlspecialchars($itemCount); ?></span>
                </a>
            </div>
        </div>

    </div>

    <!-- Navigation Menu -->
    <nav class="header-nav">
        <a href="/home">Home</a>
        <a href="/products">Products</a>
        <a href="/orders">Orders</a>
    </nav>

    <!-- Second Row: User Info & Menu Toggle -->
    <!-- <div class="header-bottom" style="width: 100%;">

        <div class="header-right">
            <button class="menu-toggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="header-left">
                <div class="user-info">
                    <span class="greet-user">
                        <i class="fa-regular fa-user"></i>
                        <h2><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                    </span>
                    <div class="popover-menu">
                        <a href="/orders">Orders</a>
                        <a href="/logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>  -->
</header>


    <script src="<?php echo $base_url; ?>/js/header.js"></script>