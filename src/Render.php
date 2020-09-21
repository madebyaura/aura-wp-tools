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
	 * Creates a new instance of `Breadcrumbs` class and invokes `render()` method.
	 *
	 * @return void
	 */
	public static function breadcrumbs() {
		$instance = new Breadcrumbs();
		$instance->render();
	}
}
