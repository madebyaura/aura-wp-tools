<?php
/**
 * Markup.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 * @since   1.0.0
 * @version 1.0.0
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Markup.
 */
class Markup {
	/**
	 * Build list of attributes into a string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Array of HTML attributes.
	 * @return string $output String of HTML attributes and values.
	 */
	public static function parse_attributes( $attributes ) {
		// Don't proceed if there are no attributes.
		if ( ! $attributes ) {
			return '';
		}

		$output = '';

		// Cycle through attributes, build tag attribute string.
		foreach ( $attributes as $key => $value ) {
			// Skip if the attribute has no value.
			if ( ! $value && 0 !== $value ) {
				continue;
			};

			if ( 'class' === $key && is_array( $value ) ) {
				$value = self::parse_classes( $value );
			}

			if ( 'style' === $key && is_array( $value ) ) {
				$value = self::parse_style_attr( $value );
				// Skip if the style attribute has no value.
				if ( ! $value ) {
					continue;
				}
			}

			if ( true === $value ) {
				$output .= esc_html( $key ) . ' ';
			} else {
				$output .= sprintf( '%s="%s" ', esc_html( $key ), call_user_func( self::get_attr_esc_function( $key ), $value ) );
			}
		}

		$output = trim( $output );

		return $output;
	}

	/**
	 * Get name of the function that should sanitize the given attribute.
	 *
	 * @since 1.0.0
	 *
	 * @param string $attribute Tag attribute.
	 * @return string Name of the function that should to sanitize.
	 */
	public static function get_attr_esc_function( $attribute ) {
		switch ( $attribute ) {
			case 'src':
			case 'href':
			case 'action':
				$function = 'esc_url';
				break;

			default:
				$function = 'esc_attr';
		}

		return $function;
	}

	/**
	 * Build list of classes into a string.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $classes  Array of CSS classes.
	 * @return string $classes String of classes separated by the space character.
	 */
	public static function parse_classes( $classes = [] ) {
		// Don't proceed if classes is empty.
		if ( ! $classes ) {
			return '';
		}

		// Pass each array element through trim().
		$classes = array_map( 'trim', $classes );

		// Remove empty array elements.
		$classes = array_filter( $classes );

		// Remove duplicate array elements.
		$classes = array_unique( $classes );

		// Separate array elements with a single space.
		$classes = implode( ' ', $classes );

		return $classes;
	}

	/**
	 * Build an array from string of classes separated by space, comma, or both.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $classes String of classes.
	 * @return array  $classes Array of classes.
	 */
	public static function explode_classes( $classes = '' ) {
		// Early exit if $classes is empty.
		if ( ! $classes ) {
			return [];
		}

		// Early exit if $classes is already an array.
		if ( is_array( $classes ) ) {
			// Remove empty elements and return.
			return array_filter( $classes );
		}

		if ( is_string( $classes ) ) {
			$classes = preg_split( '/(\\s|,)+/', $classes );
		} else {
			$classes = [];
		}

		// Remove empty elements and return.
		return array_filter( $classes );
	}

	/**
	 * Print class attribute.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $classes Array of CSS classes.
	 * @return void
	 */
	public static function class_attr( $classes = [] ) {
		// Don't proceed if classes is empty.
		if ( ! $classes ) {
			return;
		}

		echo 'class="' . esc_attr( self::parse_classes( $classes ) ) . '"';
	}

	/**
	 * Conditionally print target="_blank" attribute.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool $condition Flag indicating whether to print the attribute or not.
	 * @return void
	 */
	public static function target_attr( $condition ) {
		if ( $condition ) {
			echo 'target="_blank"';
		}
	}

	/**
	 * Parse HTML style attribute.
	 *
	 * @param string|array $properties CSS properties.
	 * @return string
	 */
	public static function parse_style_attr( $properties ) {
		if ( ! $properties ) {
			return '';
		}

		$output = '';

		if ( is_string( $properties ) ) {
			$output = $properties;
		}

		if ( is_array( $properties ) ) {
			foreach ( $properties as $key => $value ) {
				if ( ! $key || ! $value ) {
					continue;
				}

				if ( 'background-image' === $key ) {
					$output .= sprintf( '%1s: url(%2s);', $key, esc_url( $value ) );
				} else {
					$output .= sprintf( '%1s: %2s;', $key, $value );
				}
			}
		}

		return $output;
	}
}
