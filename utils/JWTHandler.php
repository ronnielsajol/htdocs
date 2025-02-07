<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Token\Plain;


$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

error_log(print_r($_ENV, true));

if (!isset($_ENV['JWT_SECRET'])) {
  error_log("JWT_SECRET is not set in the .env file.");
} else {
  error_log("JWT_SECRET is loaded successfully.");
}

class JWTHandler
{
  private static $config;

  private static function getConfig()
  {
    if (self::$config === null) {
      $secretKey = $_ENV['JWT_SECRET'] ?? '';

      if (empty($secretKey)) {
        throw new Exception('JWT secret key not set!');
      }

      self::$config = Configuration::forSymmetricSigner(
        new Sha256(),
        InMemory::plainText($secretKey)
      );
    }
    return self::$config;
  }

  public static function generateToken($user)
  {
    $config = self::getConfig();
    $now = new DateTimeImmutable();

    $token = $config->builder()
      ->issuedBy('your-app') // Optional: Set issuer
      ->permittedFor('your-audience') // Optional: Set audience
      ->identifiedBy(bin2hex(random_bytes(16))) // Unique token ID
      ->issuedAt($now)
      ->expiresAt($now->modify('+1 day')) // Token expires in 24 hours
      ->withClaim('user_id', $user['id'])
      ->withClaim('email', $user['email'])
      ->withClaim('role', $user['role'])
      ->getToken($config->signer(), $config->signingKey());

    return $token->toString();
  }

  public static function validateToken($tokenString)
  {
    try {
      $config = self::getConfig();
      $token = $config->parser()->parse($tokenString);
      assert($token instanceof Plain);

      // Validate the token signature
      if (!$config->validator()->validate($token, new SignedWith($config->signer(), $config->signingKey()))) {
        throw new Exception("Invalid token signature.");
      }

      // Check expiration
      if ($token->isExpired(new DateTimeImmutable())) {
        throw new Exception("Token has expired.");
      }

      return $token->claims()->all();
    } catch (Exception $e) {
      error_log("Error decoding token: " . $e->getMessage());
      throw new Exception("Invalid token: " . $e->getMessage());
    }
  }
}
