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
	 * @param array $args  Arguments.
	 */
	public static function breadcrumbs( $args = [] ) {
		$instance = new Breadcrumbs( $args );
		$instance->render();
	}
}
