<?php
/**
 * Utilities.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 * @since   1.0.0
 * @version 2.0.0
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Utilities.
 */
class Utils {
	/**
	 * Remove invalid characters from a string, and replace underscores,
	 * whitespace, forward slash, and back slash characters with dashes.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string - String that needs to dashified.
	 * @return string $string
	 */
	public static function dashify( $string ) {
		// Remove whitespace from the front and end, and lowercase all characters.
		$string = strtolower( trim( $string ) );

		// Remove all characters other than alphabets, numbers, dash, underscore,
		// whitespace, forward slash, and back slash.
		$string = preg_replace( '/[^a-z0-9-_\s\/\\\]/', '', $string );

		// Replace dash, underscore, whitespace, forward slash, and back slash
		// characters with dashes.
		$string = preg_replace( '/[-_\s\/\\\]+/', '-', $string );

		return $string;
	}

	/**
	 * Run all elements of an array through dashify().
	 *
	 * @since 1.0.0
	 *
	 * @param  array $array - Array to be dashified.
	 * @return array
	 */
	public static function dashify_array( $array ) {
		return array_map( __CLASS__ . '::dashify', $array );
	}

	/**
	 * Remove invalid characters from a string, and replace dash, whitespace,
	 * forward slash, and back slash characters with underscores.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string - String that needs to be underscorified.
	 * @return string $string
	 */
	public static function underscorify( $string ) {
		// Remove whitespace from the front and end, and lowercase all characters.
		$string = strtolower( trim( $string ) );

		// Remove all characters other than alphabets, numbers, dash, underscore,
		// whitespace, forward slash, and back slash.
		$string = preg_replace( '/[^a-z0-9-_\s\/\\\]/', '', $string );

		// Replace dash, underscore, whitespace, forward slash, and back slash
		// characters with underscores.
		$string = preg_replace( '/[-_\s\/\\\]+/', '_', $string );

		return $string;
	}

	/**
	 * Run all elements of an array through underscorify().
	 *
	 * @since 1.0.0
	 *
	 * @param  array $array - Array to be underscorify.
	 * @return array
	 */
	public static function underscorify_array( $array ) {
		return array_map( __CLASS__ . '::underscorify', $array );
	}

	/**
	 * Get file content.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $file_path - File path.
	 * @return string
	 */
	public static function get_file_content( $file_path ) {
		// Early exit if file doesn't exist.
		if ( ! file_exists( $file_path ) ) {
			return '';
		}

		// Return file contents using output butter.
		ob_start();
			include_once $file_path;
		return ob_get_clean();
	}

	/**
	 * Remove prefix from a string.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string - String.
	 * @param  string $prefix - Prefix to be removed.
	 * @return string
	 */
	public static function remove_prefix( $string, $prefix ) {
		return preg_replace( '/^' . $prefix . '/', '', $string );
	}

	/**
	 * Remove prefix from a array keys.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $array  - Associative array.
	 * @param  string $prefix - Prefix to be removed.
	 * @return array
	 */
	public static function remove_array_key_prefix( $array, $prefix ) {
		$output = [];

		foreach ( $array as $key => $value ) {
			$output[ self::remove_prefix( $key, $prefix ) ] = $value;
		}

		return $output;
	}

	/**
	 * Check if a plugin is active.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $plugin - Base plugin path.
	 * @return bool
	 */
	public static function is_plugin_active( $plugin ) {
		// This filed is required in order to make is_plugin_active in front end.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Use function that is defined in the global namespace.
		return \is_plugin_active( $plugin );
	}

	/**
	 * Get URL of the image.
	 *
	 * @since 1.0.0
	 *
	 * @param  integer $image_id   - Image ID.
	 * @param  string  $image_size - Image size.
	 * @return integer|string
	 */
	public static function get_image_url( $image_id, $image_size = 'thumbnail' ) {
		if ( is_numeric( $image_id ) ) {
			return wp_get_attachment_image_src( $image_id, $image_size )[0];
		} elseif ( is_string( $image_id ) ) {
			return $image_id;
		}
	}

	/**
	 * Process post query arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args - Query arguments.
	 * @return array
	 */
	public static function parse_query_args( $args ) {
		$args = self::merge_arrays( $args, [
			'query_type'       => 'latest',
			'taxonomy'         => 'category',
			'term_ids'         => '',
			'post_ids'         => '',
			'post_exclude_ids' => '',
			'posts_per_page'   => 10,
		] );

		$query_args = [];

		// Posts per page.
		$query_args['posts_per_page'] = absint( $args['posts_per_page'] );

		// Term IDs.
		if ( 'term_ids' === $args['query_type'] && $args['taxonomy'] && $args['term_ids'] ) {
			if ( 'category' === $args['taxonomy'] ) {
				$query_args['cat'] = wp_parse_id_list( $args['term_ids'] );
			} else {
				$query_args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					[
						'taxonomy' => $args['taxonomy'],
						'field'    => 'id',
						'terms'    => wp_parse_id_list( $args['term_ids'] ),
					],
				];
			}
		}

		// Post IDs.
		if ( 'post_ids' === $args['query_type'] && $args['post_ids'] ) {
			$query_args['post__in'] = wp_parse_id_list( $args['post_ids'] );
			$query_args['orderby']  = 'post__in';
		}

		// Exclude posts using post IDs.
		if ( $args['post_exclude_ids'] ) {
			$query_args['post__not_in'] = wp_parse_id_list( $args['post_exclude_ids'] );
		}

		return $query_args;
	}

	/**
	 * Get URL of the Posts Page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_posts_archive_url() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			return get_permalink( get_option( 'page_for_posts' ) );
		} else {
			return home_url();
		}
	}

	/**
	 * Get Title of the Posts Page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_posts_archive_title() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$title = get_the_title( get_option( 'page_for_posts' ) );
		} else {
			$title = get_post_type_object( 'post' )->label;
		}

		return esc_html( $title );
	}

	/**
	 * Get widget's css class name using it PHP class name.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $class_name - Widget class name.
	 * @return string $class_name - CSS class name.
	 */
	public static function get_widget_css_class( $class_name ) {
		// Early exit if class name is empty.
		if ( ! $class_name ) {
			return;
		}

		// Change string to lower case.
		$class_name = strtolower( $class_name );

		// Replace backslashes with dashes.
		$class_name = str_replace( '\\', '-', $class_name );

		// Dashify class name.
		$class_name = self::dashify( $class_name );

		return $class_name;
	}

	/**
	 * Check if a the current page is a woocommerce page.
	 *
	 * @link https://docs.woocommerce.com/document/conditional-tags/
	 *
	 * @param  string|array $page - Page or pages.
	 * @param  string|array $args - Page arguments.
	 * @return bool
	 */
	public static function is_wc_page( $page = '', $args = '' ) {
		// Early exit if the WooCommerce plugin is not active.
		if ( ! self::is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return false;
		}

		if ( is_array( $page ) ) {
			foreach ( $page as $current_page ) {
				if ( self::is_wc_page( $current_page ) ) {
					return true;
				}
			}
		} elseif ( is_string( $page ) ) {
			switch ( $page ) {
				case 'shop':
					return is_shop();

				case 'product':
					return is_product();

				case 'category':
					return is_product_category( $args );

				case 'tag':
					return is_product_tag( $args );

				case 'cart':
					return is_cart();

				case 'checkout':
					return is_checkout();

				case 'endpoint':
					return is_wc_endpoint_url( $args );

				case 'account':
					return is_account_page();

				case 'ajax':
					return is_ajax();

				default:
					return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
			}
		}

		return false;
	}
}
