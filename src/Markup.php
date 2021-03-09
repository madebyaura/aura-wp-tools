<?php
/**
 * Markup.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
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
	 * @param array $attrs  Array of HTML attributes.
	 * @return string $output  String of HTML attributes and values.
	 */
	public static function echo_attrs( $attrs ) {
		// Don't proceed if there are no attributes.
		if ( ! $attrs ) {
			return '';
		}

		// Sort attributes.
		$attrs = self::sort_attrs( $attrs );

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
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo self::esc_attr_name( $key ) . ' ';
			} else {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo sprintf( '%s="%s" ', self::esc_attr_name( $key ), call_user_func( self::get_attr_esc_function( $key ), $value ) );
			}
		}
	}

	/**
	 * Sort HTML attributes.
	 *
	 * @param array $attrs  Array of HTML attributes.
	 * @return array
	 */
	public static function sort_attrs( $attrs ) {
		// Move `href` to the begenning of the attrs.
		if ( ! empty( $attrs['href'] ) ) {
			$attrs = [ 'href' => $attrs['href'] ] + $attrs;
		}

		// Move `class` to the begenning of the attrs.
		if ( ! empty( $attrs['class'] ) ) {
			$attrs = [ 'class' => $attrs['class'] ] + $attrs;
		}

		// Move `id` to the begenning of the attrs.
		if ( ! empty( $attrs['id'] ) ) {
			$attrs = [ 'id' => $attrs['id'] ] + $attrs;
		}

		return $attrs;
	}

	/**
	 * Merge explicit HTML attributes into default attributes.
	 *
	 * @param array $default   Default HTML attributes.
	 * @param array $explicit  Explicit HTML attributes.
	 * @return array
	 */
	public static function merge_attrs( $default, $explicit ) {
		$attrs   = [];
		$classes = [];

		// If the class attribute exits in the default as well explicit attributes,
		// merge and save them into a variable.
		if ( ! empty( $default['class'] ) && ! empty( $explicit['class'] ) ) {
			// Merge attributes.
			$classes = self::merge_classes( $explicit['class'], $default['class'] );

			// Remove the class attribute from both default and expilict attributes.
			unset( $default['class'] );
			unset( $explicit['class'] );
		}

		// Merge attributes.
		$attrs = array_replace_recursive( $default, $explicit );

		// If there are classes, add them into the attributes.
		if ( $classes ) {
			$attrs['class'] = $classes;
		}

		return $attrs;
	}

	/**
	 * Parse HTML style attribute.
	 *
	 * @param string|array $properties  CSS properties.
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
	 * Escape name of the attribute.
	 *
	 * @param string $attribute  Tag attribute.
	 */
	public static function esc_attr_name( $attribute ) {
		return preg_replace( '/[^a-zA-Z0-9-]/', '', $attribute );
	}

	/**
	 * Get name of the function that should sanitize the given attribute.
	 *
	 * @param string $attribute  Tag attribute.
	 * @return string $function  Name of the function that should to sanitize.
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
	 * @param array $classes  Array of CSS classes.
	 * @return string $classes  String of classes separated by the space character.
	 */
	public static function parse_classes( $classes = [] ) {
		// Do not proceed if classes is empty.
		if ( ! $classes ) {
			return '';
		}

		// If $classes is a string, convert it into an array.
		if ( is_string( $classes ) ) {
			$classes = explode( ' ', $classes );
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
	 * Get an array from a space separated string of CSS classes.
	 *
	 * @param array|string $classes  String or array of CSS classes.
	 * @return array
	 */
	public static function get_classes_array( $classes ) {
		// If a string is provided, convert it into an array.
		if ( is_string( $classes ) ) {
			$classes = explode( ' ', $classes );
		}

		return $classes;
	}

	/**
	 * Merge classes.
	 *
	 * @param array|string $classes_1  String or array of CSS classes.
	 * @param array|string $classes_2  String or array of CSS classes.
	 * @return array
	 */
	public static function merge_classes( $classes_1, $classes_2 ) {
		// Get array of classes.
		$classes_1 = self::get_classes_array( $classes_1 );
		$classes_2 = self::get_classes_array( $classes_2 );

		// Merge arrays of classes.
		$classes = array_merge( $classes_1, $classes_2 );

		return $classes;
	}

	/**
	 * Print class attribute value.
	 *
	 * @param  array $classes  Array of CSS classes.
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
	 * @param  array $classes  Array of CSS classes.
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
	 * @param  bool $condition  Flag indicating whether to print the attribute or not.
	 * @return void
	 */
	public static function echo_target_attr( $condition ) {
		if ( $condition ) {
			echo 'target="_blank"';
		}
	}
}
