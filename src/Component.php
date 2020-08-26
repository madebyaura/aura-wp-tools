<?php
/**
 * Component.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Component.
 */
class Component {
	/**
	 * Path of the component file relative to the `$directory`.
	 *
	 * @var string Path of the component file relative to the `$directory`.
	 */
	protected $slug;

	/**
	 * Data to be passed into the component file.
	 *
	 * @var mixed $props.
	 */
	protected $props;

	/**
	 * Absolute path of the component file.
	 *
	 * @var string $path.
	 */
	protected $path;

	/**
	 * Relative path of the directory that contains components.
	 *
	 * @var string $directory.
	 */
	protected static $directory;

	/**
	 * Constructor.
	 *
	 * @param string $slug  - Path of the component file relative to the `$directory`.
	 * @param mixed  $props - Data to be passed into the component file.
	 * @return void
	 */
	public function __construct( $slug, $props = [] ) {
		$this->set_properties( $slug, $props );
	}

	/**
	 * Set class properties.
	 *
	 * @param  string $slug  - Template slug.
	 * @param  array  $props - Data to be passed into the component file.
	 * @return void
	 */
	protected function set_properties( $slug, $props ) {
		if ( ! isset( self::$directory ) ) {
			self::$directory = apply_filters( 'aura_wp_tools_component_directory', 'components' );
		}

		$this->slug  = trim( $slug, '/' );
		$this->props = $props;

		// Set absolute path of the component file.
		$this->set_path();
	}

	/**
	 * Set absolute path of the component file.
	 *
	 * @return void
	 */
	protected function set_path() {
		$relative_component_path = trailingslashit( self::$directory ) . $this->slug . '.php';

		$child_theme_path = trailingslashit( get_stylesheet_directory() ) . $relative_component_path;
		$theme_path       = trailingslashit( get_template_directory() ) . $relative_component_path;

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
	 * Checks if the component is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$enabled = true;

		if ( is_null( $this->path ) ) {
			$enabled = false;
		}

		return apply_filters( Utils::underscorify( "aura_wp_tools_component_{$this->slug}_enabled" ), $enabled );
	}

	/**
	 * Includes the component file and passes data into it.
	 *
	 * @return void
	 */
	public function render() {
		// Do not proceed if the component is not enabled.
		if ( true !== $this->is_enabled() ) {
			return;
		}

		$render = apply_filters( Utils::underscorify( "aura_wp_tools_component_{$this->slug}_render" ), true, $this->path, $this->props );

		// Make the `$props` variable available to the component.
		$props = apply_filters( Utils::underscorify( "aura_wp_tools_component_{$this->slug}_props" ), $this->props );

		// Before rendering the component.
		do_action( "aura_wp_tools_component_{$this->slug}_before", $this->path, $this->props );

		// Render the component.
		if ( true === $render ) {
			include apply_filters( Utils::underscorify( "aura_wp_tools_component_{$this->slug}_path" ), $this->path );
		}

		// After rendering the component.
		do_action( "aura_wp_tools_component_{$this->slug}_after", $this->path, $this->props );
	}
}
