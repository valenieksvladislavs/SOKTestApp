<?php

namespace App;

/**
 * Responsible for rendering templates and partials with provided context.
 */
class Renderer
{
	public function __construct() {}

	/**
	 * Renders a template snippet (partial) with the given context variables.
	 *
	 * @param string $template The partial template filename (relative to templates directory).
	 * @param array $context An associative array of variables to extract into scope.
	 * @return string The rendered partial output.
	 */
	public function render_partial(string $template, array $context = []): string
	{
		extract($context);
		ob_start();

		if (!include(__DIR__ . "/templates/$template")) {
			exit("Template Not Found");
		}

		return ob_get_clean();
	}

	/**
	 * Renders a full page using the base layout, embedding the specified template output into it.
	 *
	 * @param string $template The main content template filename.
	 * @param array $context An associative array of variables to pass to the template.
	 * @param array $addStyles An array of additional stylesheet paths to inject.
	 * @return string The fully rendered page.
	 */
	public function render(string $template, array $context = [], array $addStyles = []): string
	{
		// Extract only 'title' (and any other relevant keys) for the base layout
		$headerContext = array_intersect_key($context, array_flip(['title']));
		extract($headerContext);

		ob_start();

		// Render the main content
		$content = $this->render_partial($template, $context);

		// Then include the base layout template
		include(__DIR__ . "/templates/base-template.php");

		return ob_get_clean();
	}
}
