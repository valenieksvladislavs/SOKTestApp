<?php

namespace App;

use App\Controllers\AdminController;
use App\Controllers\HomeController;
use App\Services\SectionService;
use App\Services\UserService;
use PDO;

/**
 * Responsible for parsing the incoming request URI, determining the controller/action,
 * and dispatching control to the appropriate method with extracted parameters.
 */
class Router
{
  /**
   * @var string[] Segments of the URI (split by "/").
   */
  private array $segments = [];

  /**
   * @param string $requestUri The raw request URI (e.g., "/admin/login?param=value").
   * @param PDO $pdo The PDO connection used for passing to controllers/services.
   * @param Renderer $renderer The rendering engine for view templates.
   */
  public function __construct(
    private string $requestUri,
    private readonly PDO $pdo,
    private readonly Renderer $renderer
  ) {
    $this->parseRequestUri($requestUri);
  }

  /**
   * Splits the request URI into segments, stripping query parameters and leading/trailing slashes.
   *
   * @param string $requestUri The raw request URI.
   */
  private function parseRequestUri(string $requestUri): void
  {
    $uri = $requestUri;
    if (strpos($uri, '?') !== false) {
      $uri = strstr($uri, '?', true);
    }

    $uri = trim($uri, '/');
    $this->requestUri = $uri;

    $this->segments = ($uri !== '') ? explode('/', $uri) : [];
  }

  /**
   * Returns the array of URI segments.
   *
   * @return string[]
   */
  public function getSegments(): array
  {
    return $this->segments;
  }

  /**
   * Determines which controller and action to invoke based on the first segment of the URI.
   *
   * @return string The output from the invoked controller action.
   */
  public function dispatch(): string
  {
    // Decide if this is an admin route or a public route
    $firstSegment = $this->segments[0] ?? '';

    switch ($firstSegment) {
      case 'admin':
        $userService = new UserService($this->pdo);
        $sectionService = new SectionService($this->pdo);
        $controller = new AdminController($this->pdo, $this->renderer, $userService, $sectionService);
        $action = $this->segments[1] ?? '';
        // Remaining params after the admin route
        $remainingParams = array_slice($this->segments, 2);
        break;

      default:
        // Public route (HomeController)
        $sectionService = new SectionService($this->pdo);
        $controller = new HomeController($this->pdo, $this->renderer, $sectionService);
        $action = $firstSegment;
        // Remaining params after the first segment
        $remainingParams = array_slice($this->segments, 1);
        break;
    }

    // If no action or "index", call index
    switch ($action) {
      case '':
      case 'index':
        return $controller->actionIndex();

      default:
        // Transform kebab-case into CamelCase with "action" prefix
        $method = 'action' . str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));

        if (method_exists($controller, $method)) {
          // Reflect on the method to determine required params and variadic usage
          $reflection = new \ReflectionMethod($controller, $method);
          $parameters = $reflection->getParameters();

          $requiredCount = 0;
          $hasVariadic = false;

          foreach ($parameters as $param) {
            if ($param->isVariadic()) {
              $hasVariadic = true;
              break;
            }
            $requiredCount++;
          }

          // Ensure we have at least as many parameters as required
          if (count($remainingParams) < $requiredCount) {
            return $controller->notFound();
          }

          // If there's no variadic parameter, ensure we don't have extras
          if (!$hasVariadic && count($remainingParams) > $requiredCount) {
            return $controller->notFound();
          }

          // Invoke the controller method with the resolved parameters
          return call_user_func_array([$controller, $method], $remainingParams);
        } else {
          return $controller->notFound();
        }
    }
  }
}
