<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


error_log(print_r($_ENV, true)); // Log all environment variables

if (!isset($_ENV['JWT_SECRET'])) {
  error_log("JWT_SECRET is not set in the .env file.");
} else {
  error_log("JWT_SECRET is loaded successfully.");
}


class JWTHandler
{
  private static $secret_key;
  private static $algorithm = "HS256";

  public function __construct()
  {
    self::$secret_key = $_ENV['JWT_SECRET'];


    if (empty(self::$secret_key)) {
      throw new Exception('JWT secret key not set!');
    }
  }

  public static function generateToken($user)
  {
    $payload = [
      "user_id" => $user['id'],
      "email" => $user['email'],
      "role" => $user['role'],
      "iat" => time(),
      "exp" => time() + (60 * 60 * 24) // Token expires in 24 hours
    ];

    return JWT::encode($payload, self::$secret_key, self::$algorithm);
  }

  public static function validateToken($token)
  {
    error_log("JWT_SECRET is: " . self::$secret_key);  // Logs the secret key value

    try {
      return JWT::decode($token, new Key(self::$secret_key, self::$algorithm));
    } catch (Exception $e) {
      error_log("Error decoding token: " . $e->getMessage());
      throw new Exception("Invalid token: " . $e->getMessage());
    }
  }
}
