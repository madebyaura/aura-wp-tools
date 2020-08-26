<?php
/**
 * Render.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Render.
 */
class Render {
	/**
	 * Creates a new instance of `Component` class and invokes `render()` method.
	 *
	 * @param  string $slug  - Path of the component file relative to the `$directory`.
	 * @param  mixed  $props - Data to be passed into the template file.
	 * @return void
	 */
	public static function component( $slug, $props = [] ) {
		$instance = new Component( $slug, $props );
		$instance->render();
	}

	/**
	 * Creates a new instance of `Breadcrumbs` class and invokes `render()` method.
	 *
	 * @return void
	 */
	public static function breadcrumbs() {
		$instance = new Breadcrumbs();
		$instance->render();
	}
}
