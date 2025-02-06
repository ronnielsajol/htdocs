<?php

class AuthMiddleware
{
    /**
     * Start session if not already active
     */
    private static function startSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Redirect to a given location and exit
     */
    private static function redirectTo($location, $message = null)
    {
        if ($message) {
            $_SESSION['login_message'] = $message;
        }
        header("Location: $location");
        exit();
    }

    /**
     * Middleware for user authentication
     */
    public static function handleUserAuth()
    {
        self::startSession();

        if (!isset($_SESSION['user_id'])) {
            self::redirectTo('/', 'Please login to continue.');
        }
    }

    /**
     * Middleware for merchant authentication
     */
    public static function handleMerchantAuth()
    {
        self::startSession();

        if (!isset($_SESSION['merchant_id']) || $_SESSION['role'] !== 'merchant') {
            self::redirectTo('/merchant/login', 'Please login as a merchant to continue.');
        }
    }

    /**
     * Middleware for guest-only routes
     */
    public static function handleGuestOnly()
    {
        self::startSession();

        if (isset($_SESSION['user_id'])) {
            self::redirectTo('/');
        } elseif (isset($_SESSION['merchant_id'])) {
            self::redirectTo('/merchant/dashboard');
        } elseif (isset($_SESSION['admin_id'])) {
            self::redirectTo('/admin/dashboard');
        }
    }

    /**
     * Middleware for admin authentication
     */
    public static function handleAdminAuth()
    {
        self::startSession();

        if (!isset($_SESSION['admin_id'])) {
            self::redirectTo('/admin/login', 'Please login as an admin to continue.');
        }
    }
}
