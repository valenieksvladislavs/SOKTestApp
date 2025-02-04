<?php

namespace App\Validators;

use App\Interfaces\IValidator;
use PDO;

/**
 * Validates section data (title, slug, optional parent_id, etc.) before saving to the database.
 */
class SectionValidator implements IValidator
{
  /**
   * @var array Stores validation errors.
   */
  private array $errors = [];

  /**
   * @param PDO $pdo The PDO connection used for validation checks.
   */
  public function __construct(private readonly PDO $pdo) {}

  /**
   * Validates the given section data.
   *
   * @param array{
   *     title?: string,
   *     slug?: string,
   *     text?: string,
   *     parent_id?: string,
   *     id?: string
   * } $data The section data to validate.
   * @return bool True if valid; otherwise, false.
   */
  public function validate(array $data): bool
  {
    if (empty($data['title'])) {
      $this->addError('section', 'Please enter a section title');
    }

    if (empty($data['slug'])) {
      $this->addError('slug', 'Please enter a section slug');
    } elseif (!$this->validateSlug($data['slug'])) {
      $this->addError('slug', 'Slug must contain only lowercase Latin letters and hyphens, no spaces or other characters');
    } else {
      // Check for duplicate slug
      $sectionId = $data['id'] ?? '';
      $stmt = $this->pdo->prepare('SELECT COUNT(`id`) FROM `sections` WHERE `slug` = :slug AND `id` != :sectionId');
      $stmt->execute([
        'slug' => $data['slug'],
        'sectionId' => $sectionId
      ]);
      $count = $stmt->fetchColumn();
      if ($count) {
        $this->addError('slug', 'A section with the specified slug already exists');
      }
    }

    if (!empty($data['parent_id'])) {
      $stmt = $this->pdo->prepare("SELECT COUNT(`id`) FROM `sections` WHERE `id` = :parentId");
      $stmt->execute(['parentId' => $data['parent_id']]);
      $count = $stmt->fetchColumn();

      if (!$count) {
        $this->addError('parent_id', 'There is no such section');
      }
    }

    return empty($this->errors);
  }

  /**
   * Checks if the slug contains only lowercase letters and hyphens, and at least one letter.
   *
   * @param string $input The slug to validate.
   * @return bool True if it follows the required pattern; otherwise false.
   */
  private function validateSlug(string $input): bool
  {
    if (!preg_match('/^[a-z-]+$/', $input)) {
      return false;
    }
    return (bool)preg_match('/[a-z]/', $input);
  }
  
  public function getErrors(): array
  {
    return $this->errors;
  }

  /**
   * Adds an error message for a specific field.
   *
   * @param string $field The name of the field with an error.
   * @param string $message The error message.
   */
  protected function addError(string $field, string $message): void
  {
    $this->errors[$field][] = $message;
  }
}
