<?php
/**
 * Advanced Custom Fields.
 *
 * @link https://wordpress.org/plugins/advanced-custom-fields/
 * @link https://www.advancedcustomfields.com/pro/
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * ACF.
 */
class ACF {
	/**
	 * Get value from option ACF fields.
	 * Return default value, if ACF is not activated.
	 * Prevent fatal errors, if ACF is not activated.
	 *
	 * To get theme option value, pass 'option' as a parameter for $type.
	 * To get fields value, pass 'all_fields' as a parameter for $option_id.
	 *
	 * @param  string $group    Group.
	 * @param  string $key      Key.
	 * @param  int    $post_id  Post ID.
	 * @param  mixed  $default  Fallback value.
	 * @return mixed  $value
	 */
	public static function get_value( $group, $key, $post_id = null, $default = null ) {
		$value  = null;
		$prefix = apply_filters( 'aura_wp_tools_breadcrumbs_home_text', 'aura' );
		$key    = sanitize_key( $key );
		$id     = sanitize_key( "{$prefix}_{$group}_{$key}" );

		if ( 'all_fields' === $key ) {
			if ( function_exists( 'get_fields' ) ) {
				$value = get_fields( $post_id );
			}
		} else {
			if ( function_exists( 'get_field' ) ) {
				if ( 'option' === $group ) {
					$value = get_field( $id, 'option' );
				} else {
					$value = get_field( $id, $post_id );
				}
			}
		}

		if ( null === $value ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Parse value of the link field.
	 *
	 * @param  string $value  Field value.
	 * @return array
	 */
	public static function parse_link_value( $value ) {
		$parsed['title']    = empty( $value['title'] ) ? '' : $value['title'];
		$parsed['url']      = empty( $value['url'] ) ? '' : $value['url'];
		$parsed['external'] = empty( $value['target'] ) ? '' : $value['target'];
		$parsed['external'] = '_blank' === $parsed['external'] ? true : false;

		return $parsed;
	}

	/**
	 * Get parsed value of the link field.
	 *
	 * @param  string $group    Group.
	 * @param  string $key      Key.
	 * @param  int    $post_id  Post ID.
	 * @param  mixed  $default  Fallback value.
	 * @return mixed  $value
	 */
	public static function get_parsed_link_value( $group, $key, $post_id = null, $default = null ) {
		$value = self::get_value( $group, $key, $post_id, $default );
		$value = self::parse_link_value( $value );

		return $value;
	}

	/**
	 * Get repeater item by id.
	 *
	 * @param  string $group    group.
	 * @param  string $key      Key.
	 * @param  string $item_id  Repeater item ID.
	 * @param  string $post_id  Post ID.
	 * @return mixed
	 */
	public static function get_repeater_item_by_id( $group, $key, $item_id, $post_id = null ) {
		$value = null;
		$items = self::get_value( $group, $key, $post_id );

		foreach ( $items as $item ) {
			if ( $item_id === $item['id'] ) {
				$value = $item;

				break;
			}
		}

		return $value;
	}
}
