<?php
/**
 * Markup.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 * @since   2.1.0
 * @version 1.0.0
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Markup.
 */
class Markup {
	/**
	 * Print HTML attributes.
	 *
	 * @since 2.1.0
	 *
	 * @param array $attrs - Array of HTML attributes.
	 * @return string $output - String of HTML attributes and values.
	 */
	public static function echo_attrs( $attrs ) {
		// Don't proceed if there are no attributes.
		if ( ! $attrs ) {
			return '';
		}

		// Cycle through attributes, build tag attribute string.
		foreach ( $attrs as $key => $value ) {
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
				echo tag_escape( $key ) . ' ';
			} else {
				echo sprintf( '%s="%s" ', tag_escape( $key ), call_user_func( self::get_attr_esc_function( $key ), $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		$output = trim( $output );

		return $output;
	}

	/**
	 * Parse HTML style attribute.
	 *
	 * @since 2.1.0
	 *
	 * @param string|array $properties - CSS properties.
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

	/**
	 * Get name of the function that should sanitize the given attribute.
	 *
	 * @since 2.1.0
	 *
	 * @param string $attribute - Tag attribute.
	 * @return string $function - Name of the function that should to sanitize.
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
	 * @since 2.1.0
	 *
	 * @param array $classes - Array of CSS classes.
	 * @return string $classes - String of classes separated by the space character.
	 */
	public static function parse_classes( $classes = [] ) {
		// Do not proceed if classes is empty.
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
	 * Print class attribute value.
	 *
	 * @since 2.1.0
	 *
	 * @param  array $classes - Array of CSS classes.
	 * @return void
	 */
	public static function echo_classes( $classes = [] ) {
		// Do not proceed if classes is empty.
		if ( ! $classes ) {
			return;
		}

		echo esc_attr( self::parse_classes( $classes ) );
	}

	/**
	 * Print class attribute.
	 *
	 * @since 2.1.0
	 *
	 * @param  array $classes - Array of CSS classes.
	 * @return void
	 */
	public static function echo_class_attr( $classes = [] ) {
		// Do not proceed if classes is empty.
		if ( ! $classes ) {
			return;
		}

		echo 'class="' . esc_attr( self::parse_classes( $classes ) ) . '"';
	}

	/**
	 * Conditionally print target="_blank" attribute.
	 *
	 * @since 2.1.0
	 *
	 * @param  bool $condition - Flag indicating whether to print the attribute or not.
	 * @return void
	 */
	public static function echo_target_attr( $condition ) {
		if ( $condition ) {
			echo 'target="_blank"';
		}
	}
}
