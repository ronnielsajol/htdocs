<?php

class AuthMiddleware
{
    private static function startSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function handleUserAuth()
    {
        self::startSession();

        // Check if user is not logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['login_message'] = 'Please login to continue.';
            header('Location: /login');
            exit();
        }
    }

    public static function handleMerchantAuth()
    {
        self::startSession();

        // Check if merchant is not logged in or role is not merchant
        if (!isset($_SESSION['merchant_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'merchant') {
            $_SESSION['login_message'] = 'Please login as a merchant to continue.';
            header('Location: /merchant/login');
            exit();
        }
    }

    public static function handleGuestOnly()
    {
        self::startSession();

        // Redirect logged-in users away from login/register pages
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit();
        } else if (isset($_SESSION['merchant_id'])) {
            header('Location: /merchant/dashboard');
            exit();
        }
    }
}
