<?php

namespace App\Controllers;

use App\Exceptions\InvalidCredentialsException;
use App\Renderer;
use App\Services\UserService;
use App\Services\SectionService;
use App\Validators\LoginValidator;
use App\Validators\SectionValidator;
use Exception;
use PDO;

/**
 * Handles administrative operations such as login, logout, and section management.
 */
class AdminController extends BaseController
{
  /**
   * @param PDO $pdo
   * @param Renderer $renderer
   * @param UserService $userService
   * @param SectionService $sectionService
   */
  public function __construct(
    PDO $pdo,
    Renderer $renderer,
    private readonly UserService $userService,
    private readonly SectionService $sectionService,
  ) {
    parent::__construct($pdo, $renderer);
  }

  /**
   * @route GET /admin
   * Displays the admin panel with the list of sections.
   *
   * @return string
   */
  public function actionIndex(): string
  {
    $currentUser = $this->userService->getLoggedUser();
    if (!$currentUser) {
      header("Location: /admin/login");
    }

    $sectionsTree = $this->sectionService->getSectionsTree();
    return $this->renderer->render('admin/index.php', [
      'title' => 'Admin Panel',
      'sectionsTree' => $sectionsTree
    ]);
  }

  /**
   * @route GET|POST /admin/login
   * Shows the login form or processes login via AJAX (JSON response).
   *
   * @return string JSON or HTML depending on the request method.
   */
  public function actionLogin(): string
  {
    if (isset($_POST['login'])) {
      $validator = new LoginValidator($this->pdo);

      $data = [
        'username' => $_POST['username'] ?? '',
        'password' => $_POST['password'] ?? ''
      ];

      if (!$validator->validate($data)) {
        return json_encode([
          'success' => false,
          'errors' => $validator->getErrors(),
        ]);
      }

      try {
        $token = $this->userService->handleLogin($data);
        if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
          setcookie('auth_token', $token, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            // 'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
          ]);
        } else {
          $_SESSION['auth_token'] = $token;
        }

        return json_encode([
          'success' => true,
        ]);
      } catch (InvalidCredentialsException $error) {
        return json_encode([
          'success' => false,
          'errors' => [
            'system' => [$error->getMessage()]
          ],
        ]);
      } catch (Exception $error) {
        return json_encode([
          'success' => false,
          'errors' => [
            'system' => ['Server error or network issue occurred']
          ],
        ]);
      }
    }

    return $this->renderer->render('admin/login.php', ['title' => 'Login']);
  }

  /**
   * @route GET /admin/logout
   * Logs out the current user by clearing session and auth_token cookie.
   *
   * @return void
   */
  public function actionLogout(): void
  {
    if (isset($_COOKIE['auth_token'])) {
      unset($_COOKIE['auth_token']);
      setcookie('auth_token', '', -1, '/');
    }

    session_destroy();

    header("Location: /admin/login");
  }

  /**
   * @route GET|POST /admin/add-section
   * Shows the form to add a new section or handles form submission via AJAX (JSON response).
   *
   * @return string JSON or HTML depending on the request method.
   */
  public function actionAddSection(): string
  {
    if (isset($_POST['save'])) {
      $validator = new SectionValidator($this->pdo);
      $data = [
        'title' => $_POST['title'] ?? '',
        'slug' => $_POST['slug'] ?? '',
        'text' => $_POST['text'] ?? null,
        'parent_id' => isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? $_POST['parent_id'] : null
      ];

      if (!$validator->validate($data)) {
        $errors = $validator->getErrors();

        return json_encode([
          'success' => false,
          'errors' => $errors,
        ]);
      }

      try {
        $this->sectionService->add($data);

        return json_encode([
          'success' => true
        ]);
      } catch (Exception $error) {
        return json_encode([
          'success' => false,
          'errors' => [
            'system' => ['Server error or network issue occurred']
          ],
        ]);
      }
    }

    $sectionOptions = $this->sectionService->getAll();
    return $this->renderer->render(
      'admin/edit-section.php',
      [
        'title' => 'Add Section',
        'sectionOptions' => $sectionOptions,
        'successMessage' => 'The section has been successfully created.'
      ],
      ['/public/tinymce/skins/ui/oxide/skin.min.css']
    );
  }

  /**
   * @route GET|POST /admin/edit-section/{sectionId}
   * Shows the form to edit an existing section or handles form submission via AJAX (JSON response).
   *
   * @param string $sectionId
   * @return string JSON or HTML depending on the request method.
   */
  public function actionEditSection(string $sectionId): string
  {
    $existingSection = $this->sectionService->getById($sectionId);
    if (!$existingSection) {
      return $this->notFound();
    }

    if (isset($_POST['save'])) {
      $validator = new SectionValidator($this->pdo);
      $data = [
        'id' => $sectionId,
        'title' => $_POST['title'] ?? '',
        'slug' => $_POST['slug'] ?? '',
        'text' => $_POST['text'] ?? null,
        'parent_id' => isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? $_POST['parent_id'] : null
      ];

      if (!$validator->validate($data)) {
        $errors = $validator->getErrors();

        return json_encode([
          'success' => false,
          'errors' => $errors,
        ]);
      }

      try {
        $this->sectionService->edit($data);

        return json_encode([
          'success' => true
        ]);
      } catch (Exception $error) {
        var_dump($error);
        return json_encode([
          'success' => false,
          'errors' => [
            'system' => ['Server error or network issue occurred']
          ],
        ]);
      }
    }

    $sectionOptions = $this->sectionService->getSectionsNotDescendantsOf($sectionId);
    return $this->renderer->render(
      'admin/edit-section.php',
      [
        'title' => 'Edit Section',
        'sectionOptions' => $sectionOptions,
        'successMessage' => 'The section has been successfully updated.',
        'existingSection' => $existingSection
      ],
      ['/public/tinymce/skins/ui/oxide/skin.min.css']
    );
  }

  /**
   * @route POST /admin/delete-section/{sectionId}
   * Deletes a section via AJAX (JSON response).
   *
   * @param string $sectionId The section ID to delete.
   * @return string JSON response indicating success or error details.
   */
  public function actionDeleteSection($sectionId): string
  {
    $existingSection = $this->sectionService->getById($sectionId);
    if (!$existingSection) {
      return json_encode([
        'success' => false,
        'error' => "You're trying to delete a section that doesn't exist",
      ]);
    }

    try {
      $this->sectionService->delete($sectionId);
    } catch (Exception $error) {
      return json_encode([
        'success' => false,
        'error' => 'Server error or network issue occurred',
      ]);
    }

    return json_encode(['success' => true]);
  }
}
