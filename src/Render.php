<?php
/**
 * Render.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 * @since   1.0.0
 * @version 2.0.0
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
	 * @since 2.0.0
	 *
	 * @param  string $slug  - Path of the component file relative to the `$directory`.
	 * @param  mixed  $props - Data to be passed into the template file.
	 * @return void
	 */
	public static function component( $slug, $props = [] ) {
		$instance = new Component( $slug, $props );
		$instance->render();
	}
}
