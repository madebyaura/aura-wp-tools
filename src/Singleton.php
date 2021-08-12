<?php
/**
 * Singleton.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Singleton.
 */
abstract class Singleton {
	/**
	 * Instance.
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Get instance.
	 *
	 * Use static::$instance instead of self::$instance.
	 * self::$instance will always be parents class's static property
	 *
	 * @return object
	 */
	final public static function init() {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Constructor.
	 *
	 * Needs to be private to avoid creation of parent instance from child class.
	 */
	private function __construct(){}

	/**
	 * Restrict clone.
	 */
	final protected function __clone() {}
}
