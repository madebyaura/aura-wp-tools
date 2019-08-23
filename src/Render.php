<?php
/**
 * Render.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 * @since   1.0.0
 * @version 1.0.0
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Render.
 */
class Render {
	/**
	 * Creates a new instance of `Tag` class and invokes `render()` method.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|array $tag     Tag.
	 * @param  string|array $context Tag context.
	 * @param  string       $status  Status is the HTML tag.
	 * @param  array        $attrs   Tag attributes.
	 * @return void
	 */
	public static function tag( $tag, $context, $status = 'both', $attrs = [] ) {
		$instance = new Tag( $tag, $context, $status, $attrs );
		$instance->render();
	}

	/**
	 * Creates a new instance of `View` class and invokes `render()` method.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $slug      Path of the template file relative to the `$directory`.
	 * @param  mixed  $view_data Data to be passed into the template file.
	 * @return void
	 */
	public static function view( $slug, $view_data = [] ) {
		$instance = new View( $slug, $view_data );
		$instance->render();
	}
}
