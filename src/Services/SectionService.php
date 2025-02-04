<?php

namespace App\Services;

use PDO;
use Ramsey\Uuid\Uuid;

/**
 * Provides operations related to sections, including retrieving, adding, editing, and deleting.
 */
class SectionService
{
  /**
   * @param PDO $pdo The PDO connection for database operations.
   */
  public function __construct(private readonly PDO $pdo) {}

  /**
   * Retrieves a single section by its identifier.
   *
   * @param string $id The section's unique identifier.
   * @return array|null Returns the found section as an associative array or null if not found.
   */
  public function getById(string $id): array|null
  {
    $stmt = $this->pdo->prepare('SELECT * FROM `sections` WHERE `id` = :id');
    $stmt->execute(['id' => $id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
      return null;
    }

    return $result;
  }

  /**
   * Retrieves all sections.
   *
   * @return array An array of all sections.
   */
  public function getAll(): array
  {
    $stmt = $this->pdo->prepare("SELECT * FROM `sections`");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$result) {
      return [];
    }

    return $result;
  }

  /**
   * Returns all sections that are neither the specified section nor its descendants.
   * This is used to display valid parent options on the edit form,
   * preventing selecting itself or any child as the parent to avoid conflicts.
   *
   * @param string $sectionId The ID of the current section.
   * @return array
   */
  public function getSectionsNotDescendantsOf(string $sectionId): array
  {
    $descendantIds = $this->getAllDescendantIds($sectionId);
    $descendantIds[] = $sectionId;

    $placeholders = rtrim(str_repeat('?,', count($descendantIds)), ',');

    $sql = "SELECT * FROM `sections` WHERE `id` NOT IN ($placeholders)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($descendantIds);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Returns a tree of sections.
   *
   * @return array The hierarchical structure of sections.
   */
  public function getSectionsTree(): array
  {
    $allSections = $this->getAll();

    return $this->buildTree($allSections);
  }

  /**
   * Returns all top-level sections (those without a parent).
   *
   * @return array An array of top-level sections.
   */
  public function getTopLevelSections(): array
  {
    $stmt = $this->pdo->prepare("SELECT * FROM `sections` WHERE `parent_id` IS NULL");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$result) {
      return [];
    }

    return $result;
  }

  /**
   * For an array of slugs, retrieves the corresponding sections while ensuring that
   * each subsequent slug is a child of the previous one.
   *
   * @param string[] $slugs The array of slugs, from the topmost section to the target.
   * @return array|null Returns an array of found sections or null if the chain is invalid.
   */
  public function getBySlugs(array $slugs): ?array
  {
    $parentId = null;
    $result = [];

    foreach ($slugs as $slug) {
      $stmt = $this->pdo->prepare("
                SELECT *
                FROM sections
                WHERE slug = :slug
                  AND (
                    parent_id IS NULL
                    OR parent_id = :parentId
                  )
                LIMIT 1
            ");
      $stmt->execute([
        ':slug' => $slug,
        ':parentId' => $parentId
      ]);
      $section = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$section || $section['parent_id'] !== $parentId) {
        return null;
      }

      $result[] = $section;
      $parentId = $section['id'];
    }

    return $result;
  }

  /**
   * Finds sections by a specific parent ID.
   *
   * @param string $parentId The parent section's ID.
   * @return array|null Returns the found sections or an empty array if none.
   */
  public function getByParent(string $parentId): array|null
  {
    $stmt = $this->pdo->prepare('SELECT * FROM `sections` WHERE `parent_id` = :parentId');
    $stmt->execute(['parentId' => $parentId]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$result) {
      return [];
    }

    return $result;
  }

  /**
   * Adds a new section.
   *
   * @param array{title: string, slug: string, text?: string, parent_id?: string} $data The section data to be inserted.
   */
  public function Add(array $data): void
  {
    $id = Uuid::uuid4();
    $stmt = $this->pdo->prepare("
            INSERT INTO `sections`
            (`id`, `slug`, `title`, `text`, `parent_id`)
            VALUES
            (:id, :slug, :title, :sectionText, :parentId)
        ");
    $stmt->execute([
      'id' => $id->toString(),
      'slug' => $data['slug'],
      'title' => $data['title'],
      'sectionText' => $data['text'],
      'parentId' => $data['parent_id'],
    ]);
  }

  /**
   * Edits an existing section.
   *
   * @param array{id: string, title: string, slug: string, text?: string, parent_id: string} $data The section data to be updated.
   */
  public function Edit(array $data): void
  {
    $stmt = $this->pdo->prepare("
            UPDATE `sections`
            SET `slug` = :slug,
                `title` = :title,
                `text` = :sectionText,
                `parent_id` = :parentId
            WHERE `id` = :id
        ");
    $stmt->execute([
      'id' => $data['id'],
      'slug' => $data['slug'],
      'title' => $data['title'],
      'sectionText' => $data['text'],
      'parentId' => $data['parent_id'],
    ]);
  }

  /**
   * Deletes a section by its ID.
   *
   * @param string $id The ID of the section to delete.
   */
  public function Delete(string $id): void
  {
    $stmt = $this->pdo->prepare("DELETE FROM `sections` WHERE `id` = :id");
    $stmt->execute(['id' => $id]);
  }

  /**
   * Iteratively collects all descendant IDs of the specified section.
   *
   * @param string $sectionId The section whose descendants we want to find.
   * @return string[] An array of descendant IDs.
   */
  private function getAllDescendantIds(string $sectionId): array
  {
    $result = [];
    $queue = [$sectionId];

    while (!empty($queue)) {
      $current = array_shift($queue);

      $stmt = $this->pdo->prepare("
                SELECT `id` FROM `sections` WHERE `parent_id` = :id
            ");
      $stmt->execute(['id' => $current]);
      $children = $stmt->fetchAll(PDO::FETCH_COLUMN);

      foreach ($children as $childId) {
        $result[] = $childId;
        $queue[] = $childId;
      }
    }

    return $result;
  }

  /**
   * Recursively builds a section tree.
   *
   * @param array $elements An array of sections (each element must have 'id' and 'parent_id').
   * @param string|null $parentId The parent ID for the current level.
   * @return array The section tree.
   */
  private function buildTree(array $elements, ?string $parentId = null): array
  {
    $branch = [];

    foreach ($elements as $element) {
      if ($element['parent_id'] === $parentId) {
        $children = $this->buildTree($elements, $element['id']);
        $element['children'] = $children ?: [];
        $branch[] = $element;
      }
    }

    return $branch;
  }
}
