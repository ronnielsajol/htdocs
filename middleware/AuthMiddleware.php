<?php

require_once './utils/JWTHandler.php';

class AuthMiddleware
{
    /**
     * Get the token from the Authorization header
     */
    private static function getTokenFromHeader()
    {
        $headers = getallheaders();
        error_log("Headers: " . print_r($headers, true)); // Log all headers for debugging
        $token = $headers['Authorization'] ?? ($_GET['token'] ?? null);
        error_log("Headers: " . print_r($token, true)); // Log all headers for debugging


        if (!$token) {
            return null;
        }

        return str_replace("Bearer ", "", $token);
    }

    /**
     * Middleware for user authentication
     */
    public static function handleUserAuth()
    {
        $token = self::getTokenFromHeader();
        error_log("From User Auth error: " . print_r($token, true));

        if (!$token) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized: No token provided.", "TOKEN" => $token]);
            exit();
        }

        try {
            $decoded = JWTHandler::validateToken($token);

            if (!$decoded || !isset($decoded['user_id'])) {
                throw new Exception("Invalid token.");
            }

            // Attach user data to the request (optional)
            $_REQUEST['user'] = $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized: " . $e->getMessage()]);
            exit();
        }
    }

    /**
     * Middleware for merchant authentication
     */
    public static function handleMerchantAuth()
    {
        $token = self::getTokenFromHeader();

        if (!$token) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized: No token provided."]);
            exit();
        }

        try {
            $decoded = JWTHandler::validateToken($token);

            if (!$decoded || !isset($decoded['user_id']) || $decoded['role'] !== 'merchant') {
                throw new Exception("Unauthorized: Invalid merchant token.");
            }

            $_REQUEST['merchant'] = $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized: " . $e->getMessage()]);
            exit();
        }
    }

    /**
     * Middleware for guest-only routes
     */
    public static function handleGuestOnly()
    {
        $token = self::getTokenFromHeader();

        if ($token) {
            try {
                $decoded = JWTHandler::validateToken($token);

                if (isset($decoded['user_id'])) {
                    http_response_code(403);
                    echo json_encode(["error" => "Forbidden: Guests only."]);
                    exit();
                }
            } catch (Exception $e) {
                // If the token is invalid, allow access as a guest.
            }
        }
    }

    /**
     * Middleware for admin authentication
     */
    public static function handleAdminAuth()
    {
        $token = self::getTokenFromHeader();

        if (!$token) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized: No token provided."]);
            exit();
        }

        try {
            $decoded = JWTHandler::validateToken($token);

            if (!$decoded || !isset($decoded['user_id']) || $decoded['role'] !== 'admin') {
                throw new Exception("Unauthorized: Admin access only.");
            }

            $_REQUEST['admin'] = $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized: " . $e->getMessage()]);
            exit();
        }
    }
}
