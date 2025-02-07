<style>
   /* Footer Styling */
.footer {
    position: sticky;
    bottom: 0;
    width: 100%;
    background-color: #333;
    color: #fff;
    padding: 10px 0;
    text-align: center;
}

.footer-nav {
    display: flex;
    justify-content: center;
    gap: 40px;
}

.footer-link {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    justify-content: flex-end; /* Align text at the bottom */
}

.footer-link i {
    font-size: 20px;
    display: block; /* Ensure the icon is treated as a block-level element */
}

.footer-link span {
    font-size: 12px; /* Smaller text for the label */
}

.footer-bottom {
    margin-top: 10px;
}

.user-info {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.popover-menu {
    display: none;
    position: absolute;
    background-color: #444;
    padding: 10px;
    border-radius: 5px;
    top: 40px;
    right: 0;
}

.user-info:hover .popover-menu {
    display: block;
}

.footer-copy {
    margin-top: 10px;
    font-size: 14px;
}

/* Ensuring the footer stays at the bottom */
body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.main-content {
    flex: 1;
}

</style>

<footer class="footer">
    <div class="footer-nav">
        <a href="/home" class="footer-link">
            <i class="fas fa-home"></i>
            Home
        </a>
        <a href="/orders" class="footer-link">
            <i class="fas fa-list-alt"></i>
            Orders
        </a>
        <a href="/orders" class="footer-link">
            <i class="fa-regular fa-user"></i>
            User
        </a>
    </div>
</footer>


<script src="<?php echo $base_url; ?>/js/scripts.js"></script>
<script src="<?php echo $base_url; ?>/js/cart.js"></script>
<script src="<?php echo $base_url; ?>/js/header.js"></script>