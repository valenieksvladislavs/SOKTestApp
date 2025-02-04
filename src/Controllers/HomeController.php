<?php

namespace App\Controllers;

use App\Renderer;
use App\Services\SectionService;
use PDO;

/**
 * Manages the public-facing sections of the site, including the home page and section viewing.
 */
class HomeController extends BaseController
{
  /**
   * @param PDO $pdo
   * @param Renderer $renderer
   * @param SectionService $sectionService
   */
  public function __construct(
    PDO $pdo,
    Renderer $renderer,
    private readonly SectionService $sectionService
  ) {
    parent::__construct($pdo, $renderer);
  }

  /**
   * @route GET /
   * Displays the home page.
   *
   * @return string The rendered home page content.
   */
  public function actionIndex(): string
  {
    $header = $this->renderHeader();
    return $this->renderer->render('index.php', [
      'title' => 'Welcome!',
      'header' => $header
    ]);
  }

  /**
   * @route GET /view/...$params
   * Displays a specific section's page, including breadcrumbs, child sections, and content.
   *
   * @param string ...$params Slugs forming the path to the target section.
   * @return string The rendered section view or a 404 if the chain is invalid.
   */
  public function actionView(...$params): string
  {
    $sectionChain = $this->sectionService->getBySlugs($params);
    if (!$sectionChain) {
      return $this->notFound();
    }

    $breadcrumbs = [
      ['title' => 'Home', 'url' => '/']
    ];

    foreach ($sectionChain as $i => $section) {
      $url = '/view';
      for ($j = 0; $j <= $i; $j++) {
        $url .= '/' . $sectionChain[$j]['slug'];
      }
      $breadcrumbs[] = [
        'title' => $section['title'],
        'url'   => $url
      ];
    }

    $targetSection = end($sectionChain);
    $childSections = $this->sectionService->getByParent($targetSection['id']);
    $header = $this->renderHeader();

    return $this->renderer->render('view.php', [
      'title'         => $targetSection['title'],
      'header'        => $header,
      'breadcrumbs'   => $breadcrumbs,
      'childSections' => $childSections,
      'targetSection' => $targetSection
    ]);
  }

  /**
   * Renders the header, including top-level sections for the navigation menu.
   *
   * @return string The rendered header HTML.
   */
  private function renderHeader(): string
  {
    $topLevelSections = $this->sectionService->getTopLevelSections();
    return $this->renderer->render('header.php', [
      'topLevelSections' => $topLevelSections
    ]);
  }
}
