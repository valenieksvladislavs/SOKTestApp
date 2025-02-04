<?php

namespace App\Validators;

use App\Interfaces\IValidator;
use PDO;

/**
 * Validates login data (username and password).
 */
class LoginValidator implements IValidator
{
  /**
   * @var array Stores validation errors.
   */
  private array $errors = [];

  /**
   * @param PDO $pdo The PDO connection, if needed for any checks.
   */
  public function __construct(private readonly PDO $pdo) {}

  /**
   * Validates username and password fields.
   *
   * @param array{username?: string, password?: string} $data The login data to be validated.
   * @return bool True if valid, otherwise false.
   */
  public function validate(array $data): bool
  {
    if (empty($data['username'])) {
      $this->addError('username', 'Please enter a username');
    }

    if (empty($data['password'])) {
      $this->addError('password', 'Please provide a password');
    }

    return empty($this->errors);
  }
  
  public function getErrors(): array
  {
    return $this->errors;
  }

  /**
   * Registers an error message for a specific field.
   *
   * @param string $field The field name.
   * @param string $message The validation error message.
   */
  private function addError(string $field, string $message): void
  {
    $this->errors[$field][] = $message;
  }
}
