<?php
/**
 * View.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 * @since   1.0.0
 * @version 1.0.0
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * View.
 */
class View {
	/**
	 * Path of the template file relative to the `$directory`.
	 *
	 * @since 1.0.0
	 *
	 * @var string Path of the template file relative to the `$directory`.
	 */
	protected $slug;

	/**
	 * Data to be passed into the template file.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed $view_data.
	 */
	protected $view_data;

	/**
	 * Absolute path of the template file.
	 *
	 * @since 1.0.0
	 *
	 * @var string $path.
	 */
	protected $path;

	/**
	 * Relative path of the directory that contains views.
	 *
	 * @since 1.0.0
	 *
	 * @var string $directory.
	 */
	protected static $directory;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug      Path of the template file relative to the `$directory`.
	 * @param mixed  $view_data Arguments to be passed into the template file.
	 * @return void
	 */
	public function __construct( $slug, $view_data = [] ) {
		$this->set_properties( $slug, $view_data );
	}

	/**
	 * Set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $slug      Template slug.
	 * @param  array  $view_data Arguments to be passed into the template file.
	 * @return void
	 */
	protected function set_properties( $slug, $view_data ) {
		if ( ! isset( self::$directory ) ) {
			self::$directory = apply_filters( 'aura/wp-tools/view/directory', 'views' );
		}

		$this->slug = trim( $slug, '/' );
		$this->data = $view_data;

		// Set absolute path of the template file.
		$this->set_path();
	}

	/**
	 * Set absolute path of the template file.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function set_path() {
		$relative_view_path = trailingslashit( self::$directory ) . $this->slug . '.php';

		$child_theme_path = trailingslashit( get_stylesheet_directory() ) . $relative_view_path;
		$theme_path       = trailingslashit( get_template_directory() ) . $relative_view_path;

		if ( is_child_theme() ) {
			if ( file_exists( $child_theme_path ) ) {
				$this->path = $child_theme_path;
			}
		}

		if ( is_null( $this->path ) ) {
			if ( file_exists( $theme_path ) ) {
				$this->path = $theme_path;
			}
		}
	}

	/**
	 * Includes the template file and passes data into it.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render() {
		$enabled = apply_filters( "aura/wp-tools/view/{$this->slug}/enabled", true );

		// Early exit if the path is not set.
		if ( false === $enabled || is_null( $this->path ) ) {
			return;
		}

		// Make the `$view_data` variable available to the template.
		$view_data = apply_filters( "aura/wp-tools/view/{$this->slug}/data", $this->data );

		// Load the template.
		include apply_filters( "aura/wp-tools/view/{$this->slug}/path", $this->path );
	}
}
