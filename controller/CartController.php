<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/CartModel.php';

class CartController
{
    private $userId;

    public function __construct()
    {
        // Start the session and initialize user ID
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // For now, use a dummy user ID
        $this->userId = $_SESSION['user_id'] ?? 1;
    }

    public function addToCart()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            return [
                'success' => false,
                'message' => 'Missing product_id or quantity'
            ];
        }

        $db = new CartModel();
        $success = $db->addToCart($this->userId, $data['product_id'], $data['quantity']);
        return [
            'success' => $success,
            'message' => $success ? 'Item added to cart' : 'Failed to add item to cart'
        ];
    }

    public function updateCart()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            return [
                'success' => false,
                'message' => 'Missing product_id or quantity'
            ];
        }

        $db = new CartModel();
        $success = $db->updateCartQuantity($this->userId, $data['product_id'], $data['quantity']);
        return [
            'success' => $success,
            'message' => $success ? 'Cart updated' : 'Failed to update cart'
        ];
    }

    public function removeFromCart()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['product_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing product_id'
            ]);
            return;
        }

        $db = new CartModel();
        $success = $db->removeFromCart($this->userId, $data['product_id']);

        if ($success) {
            // Fetch updated cart total
            $cartTotal = $db->getCartTotal($this->userId);

            // Check if cart is empty
            $isCartEmpty = $db->isCartEmpty($this->userId);

            return [
                'success' => true,
                'new_total' => $cartTotal,
                'cart_empty' => $isCartEmpty,
                'message' => 'Item removed from cart'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to remove item from cart'
            ];
        }
    }

    public function getCartSummary()
    {
        $db = new CartModel();
        $cartSummary = $db->getCartSummary($this->userId);

        if (!empty($cartSummary['items'])) {
            return [
                'success' => true,
                'data' => $cartSummary
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Cart is empty'
            ];
        }
    }

    public function checkout()
    {
        $db = new CartModel();
        $response = $db->checkout($this->userId);

        if ($response['success']) {
            return [
                'success' => true,
                'message' => 'Checkout successful!',
                'order_id' => $response['order_id']
            ];
        } else {
            return [
                'success' => false,
                'message' => $response['message']
            ];
        }
    }
}
