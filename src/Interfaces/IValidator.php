<?php

namespace App\Interfaces;

/**
 * A contract for validators that check data and store any resulting errors.
 */
interface IValidator
{
  /**
   * Validates the given data.
   *
   * @param array $data The data to validate.
   * @return bool True if the data is valid, otherwise false.
   */
  public function validate(array $data): bool;

  /**
   * Returns an array of errors if validation fails.
   *
   * @return array An array of validation error messages.
   */
  public function getErrors(): array;
}
