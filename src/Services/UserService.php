<?php

namespace App\Services;

use App\Exceptions\InvalidCredentialsException;
use PDO;

/**
 * Provides methods for user authentication and retrieving the currently logged-in user.
 */
class UserService
{
  /**
   * @var array|null Stores the data of the currently logged-in user.
   */
  private ?array $loggedUser = null;

  /**
   * Initializes the UserService with a PDO instance.
   *
   * @param PDO $pdo The PDO connection for database operations.
   */
  public function __construct(private readonly PDO $pdo) {}

  /**
   * Returns the currently logged-in user's data as an associative array or null if no user is authenticated.
   *
   * @return array|null The user's data or null if no one is logged in.
   */
  public function getLoggedUser(): array|null
  {
    if ($this->loggedUser) {
      return $this->loggedUser;
    }

    if (!isset($_SESSION['auth_token']) && !isset($_COOKIE['auth_token'])) {
      return null;
    }

    $token = hash('sha256', $_SESSION['auth_token'] ?? $_COOKIE['auth_token']);
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE auth_token_hash = :token");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $this->loggedUser = $user ?? null;
    return $this->loggedUser;
  }

  /**
   * Authenticates a user by username and password. If successful, a new auth token is generated and returned.
   *
   * @param array{username: string, password: string} $data An array containing the username and password.
   * @return string A newly generated auth token.
   * @throws InvalidCredentialsException If username or password is invalid.
   */
  public function handleLogin(array $data): string
  {
    $stmt = $this->pdo->prepare("SELECT * FROM `users` WHERE `username` = :username");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !$user['id'] || !password_verify($data['password'], $user['password_hash'])) {
      throw new InvalidCredentialsException('Invalid username or password');
    }

    $newToken = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $newToken);

    $stmt = $this->pdo->prepare("UPDATE `users` SET `auth_token_hash` = :hashedToken WHERE `id` = :userId");
    $stmt->execute([
      'hashedToken' => $hashedToken,
      'userId' => $user['id']
    ]);

    return $newToken;
  }
}
