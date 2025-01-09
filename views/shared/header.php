<?php


$root_path = realpath($_SERVER["DOCUMENT_ROOT"]);
$project_root = dirname(dirname(dirname(__FILE__)));
$relative_path = str_replace($root_path, '', $project_root);
$base_url = rtrim($relative_path, '/');
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
</head>

<body>
    <header class="header">
        <a href="/home" class="logo-link">
            <h1>Stack and Shop</h1>
        </a>
        <!-- Display the username if it's set in the session -->

        <section class="header-right">
            <a href="/cart" class="cart-icon" aria-label="View shopping cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="sr-only">Cart</span>
            </a>
            <div class="user-info">
                <h2 class="greet-user"><?php echo htmlspecialchars($_SESSION['username']); ?><i class="fa-solid fa-caret-left"></i></h2>
                <div class="popover-menu"> <a href="/logout">Logout</a>
                </div>
            </div>
        </section>

    </header>

    <script src="<?php echo $base_url; ?>/js/header.js"></script>