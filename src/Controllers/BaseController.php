<?php

namespace App\Controllers;

use App\Renderer;
use PDO;

/**
 * Base controller that provides a PDO instance for database operations
 * and a Renderer instance for rendering views.
 */
abstract class BaseController
{
  /**
   * @param PDO $pdo The PDO connection used for database interactions.
   * @param Renderer $renderer The rendering engine for generating output.
   */
  public function __construct(
    protected readonly PDO $pdo,
    protected readonly Renderer $renderer
  ) {}

  /**
   * The default action method that all controllers must implement.
   *
   * @return string The rendered content for the default (index) action.
   */
  public abstract function actionIndex(): string;

  /**
   * Sends a 404 Not Found response and renders a "not found" view.
   *
   * @return string The rendered "Not Found" page content.
   */
  public function notFound(): string
  {
    http_response_code(404);
    return $this->renderer->render('not-found.html', ['title' => 'Not found']);
  }
}
