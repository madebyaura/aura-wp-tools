<?php
/**
 * Tag.
 *
 * @package MadeByAura\WPTools
 * @author  MadeByAura.com
 * @since   1.0.0
 * @version 1.0.0
 */

namespace MadeByAura\WPTools;

defined( 'ABSPATH' ) || die();

/**
 * Tag.
 */
class Tag {
	/**
	 * Prefix used for the css classes.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $tag_prefix;

	/**
	 * Default arguments for a HTML tag.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected static $defaults;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|array $tag     Tag.
	 * @param  string|array $context Tag context.
	 * @param  string       $status  Status is the HTML tag.
	 * @param  array        $attrs   Tag attributes.
	 * @return void
	 */
	public function __construct( $tag, $context, $status = 'both', $attrs = [] ) {
		/**
		 * Set class properties.
		 */
		self::set_properties();

		/**
		 * Set arguments.
		 */
		$this->args = [
			'tag'     => $tag,
			'context' => $context,
			'status'  => $status,
			'attrs'   => $attrs,
		];

		/**
		 * Merge arguments with default arguments.
		 */
		$this->args = array_replace_recursive( self::$defaults, $this->args );
	}

	/**
	 * Set class properties.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected static function set_properties() {
		if ( ! self::$tag_prefix ) {
			self::$tag_prefix = apply_filters( 'aura/wp-tools/tag/prefix', 'aura-' );
		}

		if ( ! self::$defaults ) {
			self::$defaults = [
				'tag'     => 'div',
				'context' => '',
				'status'  => 'both',
				'attrs'   => [],
			];
		}
	}

	/**
	 * Display HTML tag conditionally.
	 *
	 * @since 1.0.0
	 *
	 * @return void|string
	 */
	public function render() {
		/**
		 * Parse contexts.
		 */
		$contexts = Markup::explode_classes( $this->args['context'] );

		/**
		 * Non-contextual filter to modify arguments of HTML tag.
		 *
		 * The filter is in the format: aura/wp-tools/tag/args
		 *
		 * @since 1.0.0
		 *
		 * @param array $args Tag arguments.
		 */
		$this->args = apply_filters( 'aura/wp-tools/tag/args', $this->args );

		if ( $contexts ) {
			foreach ( $contexts as $context ) {
				/**
				 * Contextual filter to modify arguments of HTML tag.
				 *
				 * The filter is in the format: aura/wp-tools/tag/{context}/args
				 *
				 * @since 1.0.0
				 *
				 * @param array $args Tag arguments.
				 */
				$this->args = apply_filters( "aura/wp-tools/tag/{$context}/args", $this->args );
			}
		}

		/**
		 * Merge with default arguments after running $args through filters.
		 */
		$this->args = array_replace_recursive( self::$defaults, $this->args );

		/**
		 * Parse contexts again after running arguments through filters.
		 */
		$contexts = Markup::explode_classes( $this->args['context'] );

		/**
		 * Early exit if the tag is empty or the tag status is not valid.
		 */
		if ( ! $this->args['tag'] || ! in_array( $this->args['status'], [ 'open', 'close', 'both' ], true ) ) {
			return '';
		}

		/**
		 * Set tag data.
		 */
		$data['open'] = '';
		if ( in_array( $this->args['status'], [ 'open', 'both' ], true ) ) {
			$data['open'] = sprintf( '<%1s %2s>', tag_escape( $this->args['tag'] ), '%s' );
		}

		$data['close'] = '';
		if ( in_array( $this->args['status'], [ 'close', 'both' ], true ) ) {
			$data['close'] = sprintf( '</%1s>', tag_escape( $this->args['tag'] ), '%s' );
		}

		if ( $contexts ) {
			if ( $data['open'] ) {
				$data['open'] = sprintf( $data['open'], $this->attrs() );
			}

			if ( $data['close'] ) {
				// Add prefix to classes.
				$context_classes = array_map( function( $class ) {
					return '.' . self::$tag_prefix . $class;
				}, $contexts );

				$data['close'] = $data['close'];
			}

			foreach ( $contexts as $context ) {
				/**
				 * Contextual filter to modify tag data.
				 *
				 * The filter is in the format: aura/wp-tools/tag/{context}/data
				 *
				 * @since 1.0.0
				 *
				 * @param string $data Tag data.
				 * @param array  $args Tag arguments.
				 */
				$data = apply_filters( "aura/wp-tools/tag/{$context}/data", $data, $this->args );
			}
		} else {
			if ( $data['open'] ) {
				$data['open'] = sprintf( $data['open'], $this->attrs() );
			}

			/**
			 * Non-contextual filter to modify tag data.
			 *
			 * @since 1.0.0
			 *
			 * @param string $data Tag data.
			 * @param array  $args Tag arguments.
			 */
			$data = apply_filters( 'aura/wp-tools/tag/data', $data, $this->args );
		}

		if ( $data['open'] ) {
			/**
			 * Non-contextual action hook to perform actions before opening tag.
			 *
			 * The action is in the format: aura/wp-tools/tag/open/before
			 *
			 * @since 1.0.0
			 *
			 * @param string $data Tag data.
			 * @param array  $args Tag arguments.
			 */
			do_action( 'aura/wp-tools/tag/open/before', $data, $this->args );

			if ( $contexts ) {
				foreach ( $contexts as $context ) {
					/**
					 * Contextual action hook to perform actions before opening tag.
					 *
					 * The action is in the format: aura/wp-tools/tag/{context}/open/before
					 *
					 * @since 1.0.0
					 *
					 * @param string $data Tag data.
					 * @param array  $args Tag arguments.
					 */
					do_action( "aura/wp-tools/tag/{$context}/open/before", $data, $this->args );
				}
			}

			echo $data['open']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			/**
			 * Non-contextual action hook to perform actions after opening tag.
			 *
			 * The action is in the format: aura/wp-tools/tag/open/after
			 *
			 * @since 1.0.0
			 *
			 * @param string $data Tag data.
			 * @param array  $args Tag arguments.
			 */
			do_action( 'aura/wp-tools/tag/open/after', $data, $this->args );

			if ( $contexts ) {
				foreach ( $contexts as $context ) {
					/**
					 * Contextual action hook to perform actions after opening tag.
					 *
					 * The action is in the format: aura/wp-tools/tag/{context}/open/after
					 *
					 * @since 1.0.0
					 *
					 * @param string $data Tag data.
					 * @param array  $args Tag arguments.
					 */
					do_action( "aura/wp-tools/tag/{$context}/open/after", $data, $this->args );
				}
			}
		}

		if ( $data['open'] && $data['close'] ) {
			if ( $contexts ) {
				foreach ( $contexts as $context ) {
					/**
					 * Contextual action hook to perform actions between opening and closing tag.
					 *
					 * The action is in the format: aura/wp-tools/tag/{context}/content
					 *
					 * @since 1.0.0
					 *
					 * @param string $data Tag data.
					 * @param array  $args Tag arguments.
					 */
					do_action( "aura/wp-tools/tag/{$context}/content", $data, $this->args );
				}
			}
		}

		if ( $data['close'] ) {
			/**
			 * Non-contextual action hook to perform actions before closing tag.
			 *
			 * The action is in the format: aura/wp-tools/tag/close/before
			 *
			 * @since 1.0.0
			 *
			 * @param string $data Tag data.
			 * @param array  $args Tag arguments.
			 */
			do_action( 'aura/wp-tools/tag/close/before', $data, $this->args );

			if ( $contexts ) {
				/**
				 * Reverse contexts for the closing tag so that action last context is
				 * fired first.
				 */
				foreach ( array_reverse( $contexts ) as $context ) {
					/**
					 * Contextual action hook to perform actions before closing tag.
					 *
					 * The action is in the format: aura/wp-tools/tag/{context}/close/before
					 *
					 * @since 1.0.0
					 *
					 * @param string $data Tag data.
					 * @param array  $args Tag arguments.
					 */
					do_action( "aura/wp-tools/tag/{$context}/close/before", $data, $this->args );
				}
			}

			echo $data['close']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			/**
			 * Non-contextual action hook to perform actions after closing tag.
			 *
			 * The action is in the format: aura/{context}/close/after
			 *
			 * @since 1.0.0
			 *
			 * @param string $data Tag data.
			 * @param array  $args Tag arguments.
			 */
			do_action( 'aura/wp-tools/tag/close/after', $data, $this->args );

			if ( $contexts ) {
				/**
				 * Reverse contextsfor the closing tag so that action last context is
				 * fired first.
				 */
				foreach ( array_reverse( $contexts ) as $context ) {
					/**
					 * Contextual action hook to perform actions after closing tag.
					 *
					 * The action is in the format: aura/wp-tools/tag/{context}/close/after
					 *
					 * @since 1.0.0
					 *
					 * @param string $data Tag data.
					 * @param array  $args Tag arguments.
					 */
					do_action( "aura/wp-tools/tag/{$context}/close/after", $data, $this->args );
				}
			}
		}
	}

	/**
	 * Build list of attributes into a string.
	 *
	 * @since 1.0.0
	 *
	 * @return string String of HTML attributes and values.
	 */
	protected function attrs() {
		$attrs = $this->args['attrs'];

		// Parse contexts.
		$contexts = Markup::explode_classes( $this->args['context'] );

		/**
		 * If classes are supplited as space separated list, convert them into
		 * an array.
		 */
		if ( isset( $attrs['class'] ) && $attrs['class'] ) {
			if ( is_string( $attrs['class'] ) ) {
				$attrs['class'] = Markup::explode_classes( $attrs['class'] );
			}
		} else {
			$attrs['class'] = [];
		}

		/**
		 * Non-contextual filter to modify attributes.
		 *
		 * @since 1.0.0
		 *
		 * @param string $attrs Array of HTML tag attributes.
		 * @param string $args  Array of HTML tag arguments.
		 */
		$attrs = apply_filters( 'aura/wp-tools/tag/attrs/args', $attrs, $this->args );

		if ( $contexts ) {
			// Add prefix to classes.
			$context_classes = array_map( function( $class ) {
				return self::$tag_prefix . $class;
			}, $contexts );

			// Add context classes.
			$attrs['class'] = array_merge( $context_classes, $attrs['class'] );

			foreach ( $contexts as $context ) {
				/**
				 * Contextual filter to modify attributes.
				 *
				 * The filter is in the format: aura/wp-tools/tag/{context}/attrs/args
				 *
				 * @since 1.0.0
				 *
				 * @param string $attrs Array of HTML tag attributes.
				 * @param string $args  Array of HTML tag arguments.
				 */
				$attrs = apply_filters( "aura/wp-tools/tag/{$context}/attrs/args", $attrs, $this->args );
			}
		}

		// Make sure that the class is the first attribute.
		$attrs = [ 'class' => $attrs['class'] ] + $attrs;

		$output = Markup::parse_attributes( $attrs );

		/**
		 * Non-contextual filter to modify attributes output.
		 *
		 * @since 1.0.0
		 *
		 * @param string $output String of HTML tag attributes.
		 * @param string $attrs  Array of HTML tag attributes.
		 * @param string $args   Array of HTML tag arguments.
		 */
		$output = apply_filters( 'aura/wp-tools/tag/attrs/output', $output, $attrs, $this->args );

		if ( $contexts ) {
			foreach ( $contexts as $context ) {
				/**
				 * Contextual filter to modify attributes output.
				 *
				 * The filter is in the format: aura/wp-tools/tag/{context}/attrs/output
				 *
				 * @since 1.0.0
				 *
				 * @param string $output String of HTML tag attributes.
				 * @param string $attrs  Array of HTML tag attributes.
				 * @param string $args   Array of HTML tag arguments.
				 */
				$output = apply_filters( "aura/wp-tools/tag/{$context}/attrs/output", $output, $attrs, $this->args );
			}
		}

		return $output;
	}
}
